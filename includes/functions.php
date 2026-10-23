<?php
/**
 * Shared helper / auth functions.
 * Include this AFTER config/db.php in every page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function is_admin(): bool {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

/** Redirect helper */
function redirect(string $path): void {
    header("Location: $path");
    exit;
}

/** Require login on protected patient pages (pseudocode: "Else if user does not login: go to login process") */
function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        redirect('login.php');
    }
}

function require_admin(): void {
    require_login();
    if (!is_admin()) {
        redirect('dashboard.php');
    }
}

/** Escape output */
function h(?string $str): string {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** Pull + clear a one-time flash message */
function flash(string $key): ?string {
    if (!empty($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

/**
 * THE RULE ENGINE
 * Applies the if-then rules from Chapter 3.7.1 (pseudo code) / 3.7.2 (flow chart):
 *   - collects the user's yes/no answers
 *   - sums the weighted score of every "yes" answer
 *   - looks the score up against the rules table (the TB Knowledge Based System)
 * Returns ['score' => int, 'tb_status' => string, 'risk_level' => string, 'advice' => string]
 */
function run_diagnosis_rules(PDO $pdo, array $answers): array {
    // $answers = [symptom_id => 0|1]
    $symptoms = $pdo->query("SELECT symptom_id, weight FROM symptoms")->fetchAll();
    $weightMap = [];
    foreach ($symptoms as $s) {
        $weightMap[$s['symptom_id']] = (int) $s['weight'];
    }

    $score = 0;
    foreach ($answers as $symptomId => $answer) {
        if ((int) $answer === 1 && isset($weightMap[$symptomId])) {
            $score += $weightMap[$symptomId];
        }
    }

    $stmt = $pdo->prepare(
        "SELECT risk_level, tb_status, advice FROM rules
         WHERE :score BETWEEN min_score AND max_score
         LIMIT 1"
    );
    $stmt->execute(['score' => $score]);
    $rule = $stmt->fetch();

    // Fallback in case no rule row matches (should not happen with seeded data)
    if (!$rule) {
        $rule = [
            'risk_level' => 'Low',
            'tb_status'  => 'Presumptive Positive',
            'advice'     => 'Please visit Bayara Hospital for further evaluation.',
        ];
    }

    return [
        'score'      => $score,
        'tb_status'  => $rule['tb_status'],
        'risk_level' => $rule['risk_level'],
        'advice'     => $rule['advice'],
    ];
}
