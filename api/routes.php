<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once "./modules/post.php";
require_once "./config/database.php";
require_once "./modules/get.php";

$connection = new Connection();
$db = $connection->connect();
$post = new Post($db);
$get = new Get($db);

$secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";

if (isset($_REQUEST['request'])) {
    $request = explode('/', $_REQUEST['request']);
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            switch ($request[0]) {
                case 'get_student_profile':
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $profile = $get->getStudentProfile($student_id);
                    if ($profile) {
                        echo json_encode($profile);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch student profile"));
                    }
                    break;
                case 'get_student_name':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $profile = $get->getStudentName($user_id);
                    if ($profile) {
                        echo json_encode($profile);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch student profile"));
                    }
                    break;
                case 'get_employer_count':
                    $employerCount = $get->getEmployerCount();
                    if (is_int($employerCount)) {
                        echo json_encode(array("count" => $employerCount));
                    } else {
                        echo json_encode(array("message" => $employerCount));
                    }
                    break;
                case 'get_instructor_count':
                    $instructorCount = $get->getInstructorCount();
                    if (is_int($instructorCount)) {
                        echo json_encode(array("count" => $instructorCount));
                    } else {
                        echo json_encode(array("message" => $instructorCount));
                    }
                    break;
                case 'get_student_count':
                    $studentCount = $get->getStudentCount();
                    if (is_int($studentCount)) {
                        echo json_encode(array("count" => $studentCount));
                    } else {
                        echo json_encode(array("message" => $studentCount));
                    }
                    break;
                case 'get_linked_accounts_count':
                    $relationshipCount = $get->getLinkedAccountsCount();
                    if (is_int($relationshipCount)) {
                        echo json_encode(array("count" => $relationshipCount));
                    } else {
                        echo json_encode(array("message" => $relationshipCount));
                    }
                    break;
                case 'get_students_for_admin':
                    $studentRecords = $get->getStudentsForAdmin();
                    if ($studentRecords !== null) {
                        echo json_encode($studentRecords);
                    } else {
                        error_log("Failed to fetch announcement records for admin");
                        echo json_encode(array("message" => "Failed to announcement records for admin"));
                    }
                    break;
                case 'get_instructors_for_admin':
                    $instructorRecords = $get->getInstructorsForAdmin();
                    if ($instructorRecords !== null) {
                        echo json_encode($instructorRecords);
                    } else {
                        error_log("Failed to fetch records for admin");
                        echo json_encode(array("message" => "Failed to records for admin"));
                    }
                    break;
                case 'get_employers_for_admin':
                    $employerRecords = $get->getEmployersForAdmin();
                    if ($employerRecords !== null) {
                        echo json_encode($employerRecords);
                    } else {
                        error_log("Failed to fetch records for admin");
                        echo json_encode(array("message" => "Failed to records for admin"));
                    }
                    break;
                case 'get_linked_accounts_for_admin':
                    $linkedRecords = $get->getLinkedAccountsForAdmin();
                    if ($linkedRecords !== null) {
                        echo json_encode($linkedRecords);
                    } else {
                        error_log("Failed to fetch records for admin");
                        echo json_encode(array("message" => "Failed to records for admin"));
                    }
                    break;
                case 'get_student_signed_info':
                    if (!isset($_GET['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "User ID is required."));
                        break;
                    }
                    $userId = $_GET['user_id'];
                    $fileNames = array();
                    $stmt = $db->prepare("SELECT file_name FROM student_signed_endorsement_letter_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Endorsement Letter");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_signed_application_letter_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Application Letter");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_signed_parents_consent_letter_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Parent's Consent");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_signed_moa_letter_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "MOA");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_signed_acceptance_letter_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Acceptance Letter");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_resume_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Resume");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_vaccination_card_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Vaccination Card");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_barangay_clearance_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Barangay Clearance");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_medical_certificate_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "document_type" => "Medical Certificate");
                    }
                    echo json_encode(array("success" => true, "file_names" => $fileNames));
                    break;
                case 'get_past_time_records':
                    error_log("GET request received for get_past_time_records");
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    error_log("User ID received: " . $user_id);
                    $timeRecords = $get->getPastTimeRecords($user_id);
                    if ($timeRecords !== null) {
                        echo json_encode($timeRecords);
                    } else {
                        error_log("Failed to fetch past time records");
                        echo json_encode(array("message" => "Failed to fetch past time records"));
                    }
                    break;
                case 'get_proof_of_evidences_files':
                    if (!isset($_GET['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "User ID is required."));
                        break;
                    }
                    $userId = $_GET['user_id'];
                    $fileNames = array();
                    $stmt = $db->prepare("SELECT file_name FROM student_ccs_picture_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "category" => "Activities Documentation");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_acquaintance_picture_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "category" => "Acquaintance");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_seminar_certificate_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "category" => "Trainings/Seminars");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_sportsfest_picture_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "category" => "OJT Documentation");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_foundation_week_picture_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "category" => "Foundation Week");
                    }
                    $stmt = $db->prepare("SELECT file_name FROM student_documentation_tbl WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $fileNames[] = array("file_name" => $row['file_name'], "category" => "Documentation (OJT Work Photos)");
                    }
                    echo json_encode(array("success" => true, "file_names" => $fileNames));
                    break;
                case 'get_daily_accomplishments':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    if (!$user_id) {
                        echo json_encode(array("success" => false, "message" => "No user ID provided"));
                        break;
                    }
                    $accomplishments = $get->getRecordedDailyAccomplishments($user_id);
                    if ($accomplishments !== null) {
                        echo json_encode(array("success" => true, "accomplishments" => $accomplishments));
                    } else {
                        echo json_encode(array("success" => false, "message" => "No recorded daily accomplishments found"));
                    }
                    break;
                case 'get_skills_records':
                    if ($method === 'GET') {
                        if (!isset($_GET['user_id'])) {
                            echo json_encode(array("success" => false, "message" => "User ID is required"));
                            break;
                        }
                        $user_id = $_GET['user_id'];
                        $result = $get->getSkillsRecords($user_id);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'get_education_records':
                    if ($method === 'GET') {
                        if (!isset($_GET['user_id'])) {
                            echo json_encode(array("success" => false, "message" => "User ID is required"));
                            break;
                        }
                        $user_id = $_GET['user_id'];
                        $result = $get->getEducationRecords($user_id);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'get_user_photo':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $get = new Get($db);
                    $result = $get->getUserPhoto($user_id);
                    echo json_encode($result);
                    break;
                case 'get_time_in_and_out_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Employer ID: " . $employer_id);
                    $timeRecords = $get->getTimeInOutForEmployer($employer_id, $student_id);
                    if ($timeRecords !== null) {
                        echo json_encode($timeRecords);
                    } else {
                        error_log("Failed to fetch time_in and time_out records for employer");
                        echo json_encode(array("message" => "Failed to fetch time_in and time_out records for employer"));
                    }
                    break;
                case 'get_accomplishment_records_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Employer ID: " . $employer_id);
                    $accomplishmentRecords = $get->getAccomplishmentsForEmployer($employer_id, $student_id);
                    if ($accomplishmentRecords !== null) {
                        echo json_encode($accomplishmentRecords);
                    } else {
                        error_log("Failed to fetch accomplishment records for employer");
                        echo json_encode(array("message" => "Failed to fetch accomplishment records for employer"));
                    }
                    break;
                case 'get_employer_feedback':
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $feedback = $get->getEmployerFeedback($student_id);
                    if ($feedback !== null) {
                        echo json_encode($feedback);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch employer feedback"));
                    }
                    break;
                case 'get_associated_students':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $students = $get->getAssociatedStudents($employer_id);
                    if ($students !== null) {
                        echo json_encode($students);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch associated students for the employer"));
                    }
                    break;
                case 'get_uploaded_certificates':
                    $employerId = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $studentId = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $certificates = $get->getUploadedCertificates($employerId, $studentId);
                    if ($certificates !== null) {
                        echo json_encode($certificates);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch uploaded certificates for the employer"));
                    }
                    break;
                case 'get_education_records_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $educationRecords = $get->getEducationRecordsForEmployer($employer_id);
                    if ($educationRecords !== null) {
                        echo json_encode($educationRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch skill records for employer"));
                    }
                    break;
                case 'get_skills_records_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $skillsRecords = $get->getSkillsRecordsForEmployer($employer_id);
                    if ($skillsRecords !== null) {
                        echo json_encode($skillsRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch skill records for employer"));
                    }
                    break;
                case 'get_student_profile_picture':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $result = $get->getStudentProfilePicture($employer_id, $student_id);
                    echo json_encode($result);
                    break;
                case 'get_certificate_of_completion':
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    if (!$student_id) {
                        echo json_encode(array("success" => false, "message" => "Student ID is required."));
                        break;
                    }
                    $result = $get->getCertificateOfCompletionForStudent($student_id);
                    echo json_encode($result);
                    break;
                case 'get_time_in_and_out_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $dtrRecords = $get->getStudentDTRForInstructor($instructor_id, $student_id);
                    if ($dtrRecords !== null) {
                        echo json_encode($dtrRecords);
                    } else {
                        error_log("Failed to fetch time_in and time_out records for instructor");
                        echo json_encode(array("message" => "Failed to fetch time_in and time_out records for instructor"));
                    }
                    break;
                case 'get_associated_students_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $students = $get->getAssociatedStudentsForInstructor($instructor_id);
                    if ($students !== null) {
                        echo json_encode($students);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch associated students for the instructor"));
                    }
                    break;
                case 'get_instructor_name':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $instructor_name = $get->getInstructorName($instructor_id);
                    if ($instructor_name) {
                        echo json_encode($instructor_name);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch instructor profile"));
                    }
                    break;
                case 'get_employer_name':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $employer_name = $get->getEmployerName($employer_id);
                    if ($employer_name) {
                        echo json_encode($employer_name);
                    } else {
                        echo json_encode(array("message" => "Failed to fetch employer profile"));
                    }
                    break;
                case 'get_student_profile_picture_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $result = $get->getStudentProfilePictureForInstructor($instructor_id, $student_id);
                    echo json_encode($result);
                    break;
                case 'get_student_accomplishments_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $accomplishmentRecords = $get->getStudentAccomplishmentsForInstructor($instructor_id, $student_id);
                    if ($accomplishmentRecords !== null) {
                        echo json_encode($accomplishmentRecords);
                    } else {
                        error_log("Failed to fetch time_in and time_out records for instructor");
                        echo json_encode(array("message" => "Failed to accomplishments records for instructor"));
                    }
                    break;
                case 'get_student_certificates':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $certificateRecords = $get->getStudentCertificatesForInstructor($instructor_id, $student_id);
                    if ($certificateRecords !== null) {
                        echo json_encode($certificateRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to retrieve certificate records for instructor"));
                    }
                    break;
                case 'get_student_application_letter':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentApplicationLetter($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_acceptance_letter':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentAcceptanceLetter($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_moa_letter':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentMOA($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_medical_certificate':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentMedicalCertificate($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_vaccination_card':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentVaccinationCard($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_barangay_clearance':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentBarangayClearance($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_resume':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentResume($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_endorsement_letter':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentEndorsementLetter($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_parents_consent':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentParentsConsent($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_employer_feedback_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $feedbackRecords = $get->getEmployerFeedbackForInstructor($instructor_id, $student_id);
                    if ($feedbackRecords !== null) {
                        echo json_encode($feedbackRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to feedback records for instructor"));
                    }
                    break;
                case 'get_student_ccs_poe':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentCCSPOE($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_acquaintance_poe':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentAcquaintancePOE($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_seminar_poe':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentSeminarPOE($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_dtr':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    error_log("User ID: " . $user_id);
                    $documentRecords = $get->getDTRForStudent($user_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_weekly_accomplishments':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    error_log("User ID: " . $user_id);
                    $documentRecords = $get->getWeeklyAccomplishmentsForStudent($user_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_documentation':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    error_log("User ID: " . $user_id);
                    $documentRecords = $get->getDocumentationForStudent($user_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_foundationweek_poe':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentFoundationWeekPOE($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_student_gc_events_poe':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentRecords = $get->getStudentGCEventsPOE($instructor_id, $student_id);
                    if ($documentRecords !== null) {
                        echo json_encode($documentRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_requirement_status_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $statusRecords = $get->getRequirementStatusForInstructor($instructor_id, $student_id);
                    if ($statusRecords !== null) {
                        echo json_encode($statusRecords);
                    } else {
                        error_log("Failed to fetch status records for instructor");
                        echo json_encode(array("message" => "Failed to status records for instructor"));
                    }
                    break;
                case 'get_requirement_status_for_student':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $statusRecords = $get->getRequirementStatusForStudent($user_id);
                    if ($statusRecords !== null) {
                        echo json_encode($statusRecords);
                    } else {
                        error_log("Failed to status records for student");
                        echo json_encode(array("message" => "Failed to status records for student"));
                    }
                    break;
                case 'get_announcements_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $announcementRecords = $get->getAnnouncements($instructor_id);
                    if ($announcementRecords !== null) {
                        echo json_encode($announcementRecords);
                    } else {
                        error_log("Failed to fetch announcement records for instructor");
                        echo json_encode(array("message" => "Failed to announcement records for instructor"));
                    }
                    break;
                case 'get_announcements_for_student':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $announcementRecords = $get->getAnnouncementsForStudent($user_id);
                    if ($announcementRecords !== null) {
                        echo json_encode($announcementRecords);
                    } else {
                        error_log("Failed to fetch announcement records for student");
                        echo json_encode(array("message" => "Failed to announcement records for student"));
                    }
                    break;
                case 'get_final_report_for_student':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $finalReportRecords = $get->getFinalReportForStudent($user_id);
                    if ($finalReportRecords !== null) {
                        echo json_encode($finalReportRecords);
                    } else {
                        error_log("Failed to fetch announcement records for student");
                        echo json_encode(array("message" => "Failed to announcement records for student"));
                    }
                    break;
                case 'get_dtr_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $dtrRecords = $get->getDTRForInstructor($instructor_id, $student_id);
                    if ($dtrRecords !== null) {
                        echo json_encode($dtrRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_final_report_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $finalReportRecords = $get->getFinalReportForInstructor($instructor_id, $student_id);
                    if ($finalReportRecords !== null) {
                        echo json_encode($finalReportRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_weekly_accomplishments_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $weeklyAccomplishmentsRecords = $get->getWeeklyAccomplishmentsForInstructor($instructor_id, $student_id);
                    if ($weeklyAccomplishmentsRecords !== null) {
                        echo json_encode($weeklyAccomplishmentsRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_documentation_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Instructor ID: " . $instructor_id);
                    $documentationRecords = $get->getDocumentationForInstructor($instructor_id, $student_id);
                    if ($documentationRecords !== null) {
                        echo json_encode($documentationRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for instructor"));
                    }
                    break;
                case 'get_weekly_accomplishments_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Employer ID: " . $employer_id);
                    $weeklyAccomplishmentsRecords = $get->getWeeklyAccomplishmentsForEmployer($employer_id, $student_id);
                    if ($weeklyAccomplishmentsRecords !== null) {
                        echo json_encode($weeklyAccomplishmentsRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for employer"));
                    }
                    break;
                case 'get_dtr_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    error_log("Employer ID: " . $employer_id);
                    $dtrRecords = $get->getDTRForEmployer($employer_id, $student_id);
                    if ($dtrRecords !== null) {
                        echo json_encode($dtrRecords);
                    } else {
                        echo json_encode(array("message" => "Failed to load document records for employer"));
                    }
                    break;
                case 'get_exit_poll':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $exitPollRecords = $get->getExitPoll($user_id);
                    echo json_encode($exitPollRecords);
                    break;

                case 'get_exit_poll_for_instructor':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $exitPollRecords = $get->getExitPollForInstructor($instructor_id, $student_id);
                    echo json_encode($exitPollRecords);
                    break;

                case 'get_exit_poll_for_employer':
                    $employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $exitPollRecords = $get->getExitPollForEmployer($employer_id, $student_id);
                    echo json_encode($exitPollRecords);
                    break;

                case 'get_all_requirements':
                    $instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;
                    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
                    $requirementRecords = $get->getAllRequirements($instructor_id, $student_id);
                    echo json_encode($requirementRecords);
                    break;


                default:
                    echo "Not Found";
                    http_response_code(404);
                    break;
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            switch ($request[0]) {
                case 'student_create_account':
                    $result = $post->studentCreateAccount($data);
                    if ($result['success']) {
                        echo json_encode(array("message" => "Instructor account created successfully."));
                    } else {
                        echo json_encode(array("message" => "Failed to create student account"));
                    }
                    break;
                case 'instructor_create_account':
                    $result = $post->instructorCreateAccount($data);
                    if ($result['success']) {
                        echo json_encode(array("message" => "Instructor account created successfully."));
                    } else {
                        echo json_encode(array("message" => "Failed to create instructor account"));
                    }
                    break;
                case 'employer_create_account':
                    $result = $post->employerCreateAccount($data);
                    if ($result) {
                        echo json_encode(array("message" => "Employer account created successfully."));
                    } else {
                        echo json_encode(array("message" => "Failed to create employer account. Ensure sure no Email and Student School ID exists."));
                    }
                    break;
                case 'admin_login':
                    if ($method === 'POST') {
                        $result = $post->admin_login($data);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'student_login':
                    if ($method === 'POST') {
                        $result = $post->student_login($data);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'employer_login':
                    if ($method === 'POST') {
                        echo json_encode($post->employer_login($data));
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'instructor_login':
                    if ($method === 'POST') {
                        $email = $data['email'];
                        $password = $data['password'];
                        echo json_encode($post->instructor_login($data));
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'link_student_and_employer':
                    $result = $post->linkStudentAndEmployer($data);
                    if ($result['success']) {
                        echo json_encode(array("message" => "Accounts linked successfully."));
                    } else {
                        echo json_encode(array("message" => "Failed to link accounts"));
                    }
                    break;
                case 'delete_student':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteStudent($data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_instructor':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['instructor_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteInstructor($data['instructor_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_employer':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['employer_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteEmployer($data['employer_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_linked_account':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['employer_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteLinkedAccount($data['employer_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'student_update_information':
                    echo json_encode($post->updateInformation($data));
                    break;
                case 'update_student_mobile_number':
                    echo json_encode($post->updateStudentMobileNumber($data));
                    break;
                case 'update_student_company_address':
                    echo json_encode($post->updateStudentCompanyAddress($data));
                    break;
                case 'upload_endorsement_letter':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadSignedEndorsementLetter('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_moa':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadSignedMOA('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_acceptance_letter':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadSignedAcceptanceLetter('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_resume':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadResume('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_medical_certificate':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadMedicalCertificate('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_vaccination_card':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadVaccinationCard('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_barangay_clearance':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadBarangayClearance('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'upload_application_letter':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadSignedApplicationLetter('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_parents_consent_letter':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $result = $post->uploadSignedParentsConsentLetter('file', $user_id);
                    echo json_encode($result);
                    break;
                case 'record_time_in':
                    if (!isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "User ID is required."));
                        break;
                    }
                    $user_id = $data['user_id'];
                    $result = $post->record_time_in($user_id);
                    echo json_encode($result);
                    break;
                case 'record_time_out':
                    if (!isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "User ID is required."));
                        break;
                    }
                    $user_id = $data['user_id'];
                    $result = $post->record_time_out($user_id);
                    echo json_encode($result);
                    break;
                case 'upload_ccs_picture':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadCCSPicture('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_file_dtr':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadDTR('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_weekly_accomplishments':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadWeeklyAccomplishments('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_documentation':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadDocumentation('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_seminar_certificate':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadSeminarCertificate('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_final_report':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadFinalReport('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'upload_sportsfest_picture':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadSportsfestPicture('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'delete_proof_of_evidence_file':
                    $json_data = file_get_contents('php://input');
                    $request_data = json_decode($json_data, true);
                    if (!isset($request_data['file_name']) || !isset($request_data['category']) || !isset($request_data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "File name, category, and user ID are required."));
                        break;
                    }
                    $fileName = $request_data['file_name'];
                    $category = $request_data['category'];
                    $userId = $request_data['user_id'];
                    $result = $post->deleteProofOfEvidenceFile($fileName, $category, $userId, $db);
                    echo json_encode($result);
                    break;
                case 'record_daily_accomplishments':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    $result = $post->insertDailyAccomplishment($data);
                    echo json_encode($result);
                    break;
                case 'delete_daily_accomplishments':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['daily_accomplishments_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteDailyAccomplishment($data['daily_accomplishments_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_skills':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['portfolio_skills_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteSkills($data['portfolio_skills_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_dtr':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['dtr_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteDTR($data['dtr_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_certificate':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['file_id']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteCertificate($data['file_id'], $data['employer_id'], $data['student_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_dtr_file':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['file_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteDTRFile($data['file_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_final_report':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['file_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteFinalReport($data['file_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_weekly_accomplishments':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['file_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteWeeklyAccomplishments($data['file_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_documentation':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['file_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteDocumentation($data['file_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'delete_education':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['portfolio_education_id']) || !isset($data['user_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteEducation($data['portfolio_education_id'], $data['user_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'record_skills_portfolio':
                    if ($method === 'POST') {
                        $request_body = file_get_contents('php://input');
                        if (!$request_body) {
                            echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                            break;
                        }
                        $data = json_decode($request_body, true);
                        $result = $post->insertSkillsRecord($data);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'record_education_portfolio':
                    if ($method === 'POST') {
                        $request_body = file_get_contents('php://input');
                        if (!$request_body) {
                            echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                            break;
                        }
                        $data = json_decode($request_body, true);
                        $result = $post->insertEducationRecord($data);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'upload_user_photo':
                    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;
                    $school_id = isset($_GET['school_id']) ? $_GET['school_id'] : null;
                    $result = $post->uploadUserPhoto('file', $user_id, $school_id);
                    echo json_encode($result);
                    break;
                case 'update_dtr_status':
                    if (!isset($data['dtr']['dtr_id']) || !isset($data['dtr']['status']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required parameters."));
                        break;
                    }
                    $employerId = $data['employer_id'];
                    $studentId = $data['student_id'];
                    $result = $post->updateDTRStatus($data['dtr'], $employerId, $studentId);
                    echo json_encode($result);
                    break;
                case 'update_ojt_status':
                    if (!isset($data['instructor_id']) || !isset($data['student_id']) || !isset($data['status'])) {
                        echo json_encode(["success" => false, "message" => "Missing required parameters."]);
                        break;
                    }
                    $instructorId = $data['instructor_id'];
                    $studentId = $data['student_id'];
                    $status = $data['status'];
                    $result = $post->updateOJTStatus($instructorId, $studentId, $status);
                    echo json_encode($result);
                    break;
                case 'update_dtr_file_status':
                    if (!isset($data['dtr']['file_id']) || !isset($data['dtr']['status']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required parameters."));
                        break;
                    }
                    $employerId = $data['employer_id'];
                    $studentId = $data['student_id'];
                    $result = $post->updateDTRFileStatus($data['dtr'], $employerId, $studentId);
                    echo json_encode($result);
                    break;
                case 'update_weekly_accomplishments_file_status':
                    if (!isset($data['weekly_accomplishments']['file_id']) || !isset($data['weekly_accomplishments']['status']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required parameters."));
                        break;
                    }
                    $employerId = $data['employer_id'];
                    $studentId = $data['student_id'];
                    $result = $post->updateWeeklyAccomplishmentsFileStatus($data['weekly_accomplishments'], $employerId, $studentId);
                    echo json_encode($result);
                    break;
                case 'update_final_report_status':
                    if (!isset($data['report']['file_id']) || !isset($data['report']['status']) || !isset($data['instructor_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required parameters."));
                        break;
                    }
                    $instructorId = $data['instructor_id'];
                    $studentId = $data['student_id'];
                    $result = $post->updateFinalReportStatus($data['report'], $instructorId, $studentId);
                    echo json_encode($result);
                    break;
                case 'update_documentation_status':
                    if (!isset($data['documentation']['file_id']) || !isset($data['documentation']['status']) || !isset($data['instructor_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required parameters."));
                        break;
                    }
                    $instructorId = $data['instructor_id'];
                    $studentId = $data['student_id'];
                    $result = $post->updateDocumentationStatus($data['documentation'], $instructorId, $studentId);
                    echo json_encode($result);
                    break;
                case 'update_accomplishment_status':
                    if (!isset($data['accomplishment']['daily_accomplishments_id']) || !isset($data['accomplishment']['status']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(["success" => false, "message" => "Missing required parameters."]);
                        break;
                    }

                    $employerId = $data['employer_id'];
                    $studentId = $data['student_id'];
                    $result = $post->updateAccomplishmentStatus($data['accomplishment'], $employerId, $studentId);
                    echo json_encode($result);
                    break;
                case 'insert_employer_feedback':
                    if ($method === 'POST') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        $result = $post->insertEmployerFeedback($data);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'upload_certificate':
                    $employer_id = isset($_POST['employer_id']) ? $_POST['employer_id'] : null;
                    $student_id = isset($_POST['student_id']) ? $_POST['student_id'] : null;
                    $result = $post->uploadCertificate($employer_id, $student_id);
                    echo json_encode($result);
                    break;
                case 'update_requirements_status':
                    if (!isset($data['student_id']) || !isset($data['instructor_id']) || !isset($data['status_updates'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required parameters."));
                        break;
                    }
                    $studentId = $data['student_id'];
                    $instructorId = $data['instructor_id'];
                    $statusUpdates = $data['status_updates'];
                    $result = $post->updateRequirementsStatus($studentId, $instructorId, $statusUpdates);
                    echo json_encode($result);
                    break;
                case 'insert_instructor_announcement':
                    if ($method === 'POST') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        $result = $post->makeAnnouncement($data);
                        echo json_encode($result);
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;
                case 'update_dtr_remarks':
                    if (!isset($data['dtr_id']) || !isset($data['student_id']) || !isset($data['employer_id']) || !isset($data['remarks'])) {
                        echo json_encode(["success" => false, "message" => "Missing required parameters."]);
                        break;
                    }
                    $result = $post->updateDTRRemarks($data);
                    echo json_encode($result);
                    break;

                case 'delete_announcement':
                    $request_body = file_get_contents('php://input');
                    if (!$request_body) {
                        echo json_encode(array("success" => false, "message" => "No data provided in the request body"));
                        break;
                    }
                    $data = json_decode($request_body, true);
                    if (!isset($data['instructor_id']) || !isset($data['announcement_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields in request data"));
                        break;
                    }
                    $result = $post->deleteAnnouncement($data['instructor_id'], $data['announcement_id']);
                    echo json_encode(array("success" => $result));
                    break;
                case 'update_time_in':
                    if (!isset($data['dtr_id']) || !isset($data['time_in']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields"));
                        break;
                    }
                    $result = $post->updateTimeIn($data);
                    echo json_encode($result);
                    break;
                case 'update_time_out':
                    if (!isset($data['dtr_id']) || !isset($data['time_out']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
                        echo json_encode(array("success" => false, "message" => "Missing required fields"));
                        break;
                    }
                    $result = $post->updateTimeOut($data);
                    echo json_encode($result);
                    break;

                case 'insert_exit_poll':
                    if ($method === 'POST') {
                        if (!isset($data['user_id'])) {
                            echo json_encode(array("success" => false, "message" => "User ID is required."));
                            break;
                        }
                        $result = $post->insertExitPoll($data);
                        if ($result) {
                            echo json_encode(array("success" => true, "message" => "Exit poll submitted successfully."));
                        } else {
                            echo json_encode(array("success" => false, "message" => "Failed to submit exit poll."));
                        }
                    } else {
                        echo "Method Not Allowed";
                        http_response_code(405);
                    }
                    break;

                case 'edit_exit_poll':
                    $result = $post->editExitPoll($data);
                    echo json_encode($result);
                    break;

                default:
                    echo "Not Found";
                    http_response_code(404);
                    break;
            }
            break;
        default:
            echo "Method Not Allowed";
            http_response_code(405);
            break;
    }
} else {
    echo "Not Found";
    http_response_code(404);
}
