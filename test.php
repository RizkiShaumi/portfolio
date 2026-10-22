<?php
declare(strict_types=1);

// Test 1: validate_contact behavior
ob_start();
require_once __DIR__ . '/index.php';
ob_end_clean();

// Assert CSRF failure
$res = validate_contact(['csrf_token' => 'wrong', 'name' => 'Alice', 'email' => 'a@b.com', 'message' => 'Hello test'], 'secret_token');
assert($res['ok'] === false, 'CSRF failure must return ok=false');

// Assert email validation
$res = validate_contact(['csrf_token' => 'tok', 'name' => 'Alice', 'email' => 'not-an-email', 'message' => 'Hello test'], 'tok');
assert($res['ok'] === false, 'Invalid email must return ok=false');

// Assert short name rejection
$res = validate_contact(['csrf_token' => 'tok', 'name' => 'A', 'email' => 'a@b.com', 'message' => 'Hello test'], 'tok');
assert($res['ok'] === false, 'Short name must return ok=false');

// Assert valid input acceptance
$res = validate_contact(['csrf_token' => 'tok', 'name' => 'Rizki', 'email' => 'rizki@example.com', 'message' => 'Testing message body.'], 'tok');
assert($res['ok'] === true, 'Valid payload must return ok=true');
assert($res['entry']['name'] === 'Rizki', 'Entry name must match');

// Test 2: resume.pdf existence
assert(file_exists(__DIR__ . '/resume.pdf'), 'resume.pdf must exist');
assert(filesize(__DIR__ . '/resume.pdf') > 1000, 'resume.pdf must be non-empty');

// Test 3: Data structures
assert(isset($profile['name']), 'Profile name must be defined');
assert(count($projects) >= 4, 'Must have at least 4 projects');
assert(count($experiences) >= 1, 'Must have at least 1 work experience');
assert(count($certs) >= 4, 'Must have at least 4 certifications');

echo "ALL TESTS PASSED.\n";
