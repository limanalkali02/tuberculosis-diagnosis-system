<?php
require_once 'config/db.php';
require_once 'includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('diagnose.php');
}

$symptoms = $pdo->query('SELECT symptom_id FROM symptoms')->fetchAll();

$answers = [];
foreach ($symptoms as $s) {
    $field = 'symptom_' . $s['symptom_id'];
    if (!isset($_POST[$field])) {
        // pseudocode: "Else if user does not answer all questions"
        $_SESSION['flash_error'] = 'Please answer every question before submitting.';
        redirect('diagnose.php');
    }
    $answers[$s['symptom_id']] = (int) $_POST[$field];
}

// Apply if-then rules (the TB Knowledge Based System / rule engine)
$result = run_diagnosis_rules($pdo, $answers);

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare(
        'INSERT INTO diagnoses (user_id, total_score, tb_status, risk_level, advice)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([
        $_SESSION['user_id'],
        $result['score'],
        $result['tb_status'],
        $result['risk_level'],
        $result['advice'],
    ]);
    $diagnosisId = $pdo->lastInsertId();

    $stmt = $pdo->prepare(
        'INSERT INTO diagnosis_answers (diagnosis_id, symptom_id, answer) VALUES (?, ?, ?)'
    );
    foreach ($answers as $symptomId => $answer) {
        $stmt->execute([$diagnosisId, $symptomId, $answer]);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_error'] = 'Something went wrong saving your result. Please try again.';
    redirect('diagnose.php');
}

redirect('result.php?id=' . $diagnosisId);
