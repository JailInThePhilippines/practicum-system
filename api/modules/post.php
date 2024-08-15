<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use \Firebase\JWT\JWT;

class Post
{
    private $db;
    private $secret_key;

    public function __construct($db)
    {
        $this->db = $db;
        $this->secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
    }

    public function studentCreateAccount($data)
    {
        $student_name = $data['student_name'];
        $student_password = $data['student_password'];
        $school_id = $data['school_id'];
        $student_email = $data['student_email'];
        $role = $data['role'];
        $student_mobile_number = $data['student_mobile_number'];
        $block = $data['block'];
        $program = $data['program'];
        $company_address = $data['company_address'];
        $student_year = $data['student_year'];
        $admin_id = $data['admin_id'];

        // Check if the provided admin_id exists in the admin_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM admin_credentials_tbl WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // If admin_id doesn't exist, return error
        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Invalid admin ID. Cannot create student account.'
            ];
        }

        // Check if the student's email already exists in student_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM student_credentials_tbl WHERE student_email = ?");
        $stmt->execute([$student_email]);
        $existing_student = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the email already exists, return error
        if ($existing_student) {
            return [
                'success' => false,
                'message' => 'Student account with this email already exists.'
            ];
        }

        // Hash the password for security
        $hashed_password = password_hash($student_password, PASSWORD_DEFAULT);

        // Prepare SQL statement to insert student with default ojt_status
        $stmt = $this->db->prepare("INSERT INTO student_credentials_tbl (student_name, student_password, school_id, student_email, role, student_mobile_number, block, program, company_address, student_year, ojt_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters and execute the statement
        $stmt->execute([$student_name, $hashed_password, $school_id, $student_email, $role, $student_mobile_number, $block, $program, $company_address, $student_year, 'Not Yet Done']);

        // Check if insertion was successful
        if ($stmt->rowCount() > 0) {
            // Get the newly created student ID
            $student_id = $this->db->lastInsertId();

            // Find matching instructors
            $stmt = $this->db->prepare("
            SELECT instructor_id 
            FROM instructor_credentials_tbl 
            WHERE FIND_IN_SET(?, block_handled) 
            AND FIND_IN_SET(?, program_handled) 
            AND FIND_IN_SET(?, year_handled)
        ");
            $stmt->execute([$block, $program, $student_year]);
            $matching_instructors = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Insert relationships into student_instructor_relationship_tbl
            foreach ($matching_instructors as $instructor) {
                $instructor_id = $instructor['instructor_id'];
                $stmt = $this->db->prepare("INSERT INTO student_instructor_relationship_tbl (student_id, instructor_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $instructor_id]);

                // Insert into instructor_requirement_checking_tbl with default values
                $stmt = $this->db->prepare("INSERT INTO instructor_requirement_checking_tbl (
                endorsement_status,
                application_status,
                consent_status,
                acceptance_status,
                moa_status,
                vaccination_status,
                barangay_status,
                medical_status,
                resume_status,
                accomplishment_status,
                ccs_status,
                acquaintance_status,
                seminar_status,
                sportsfest_status,
                foundation_status,
                instructor_id,
                student_id
            ) VALUES (
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                'Not Yet Cleared',
                ?,
                ?
            )");
                $stmt->execute([$instructor_id, $student_id]);
            }
            return [
                'success' => true,
                'message' => 'Student account created successfully and relationships established.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create student account.'
            ];
        }
    }

    public function employerCreateAccount($data)
    {
        $employer_name = $data['employer_name'];
        $employer_email = $data['employer_email'];
        $employer_password = $data['employer_password'];
        $role = $data['role'];
        $company_name = $data['company_name'];
        $employer_position = $data['employer_position'];
        $company_number    = $data['company_number'];
        $company_address = $data['company_address'];
        $company_email = $data['company_email'];
        $admin_id = $data['admin_id'];

        // Check if the provided admin_id exists in the admin_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM admin_credentials_tbl WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // If admin_id doesn't exist, return error
        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Invalid admin ID. Cannot create employer account.'
            ];
        }

        // Check if the employer's email already exists in instructor_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM employer_credentials_tbl WHERE employer_email = ?");
        $stmt->execute([$employer_email]);
        $existing_employer = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the email already exists, return error
        if ($existing_employer) {
            return [
                'success' => false,
                'message' => 'Employer account with this email already exists.'
            ];
        }

        // Hash the password for security
        $hashed_password = password_hash($employer_password, PASSWORD_DEFAULT);

        // Prepare SQL statement
        $stmt = $this->db->prepare("INSERT INTO employer_credentials_tbl (employer_name, employer_email, employer_password, role, company_name, employer_position, company_number, company_address, company_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters and execute the statement
        $stmt->execute([$employer_name,  $employer_email, $hashed_password, $role, $company_name, $employer_position, $company_number, $company_address, $company_email]);

        // Check if insertion was successful
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Employer account created successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create employer account.'
            ];
        }
    }

    public function instructorCreateAccount($data)
    {
        // Extract data from $data array
        $instructor_email = $data['instructor_email'];
        $instructor_password = $data['instructor_password'];
        $role = $data['role'];

        // Check if block_handled is an array before using implode
        if (is_array($data['block_handled'])) {
            $block_handled = implode(',', $data['block_handled']); // Convert array to comma-separated string
        } else {
            $block_handled = $data['block_handled'];
        }

        // Check if program_handled is an array before using implode
        if (is_array($data['program_handled'])) {
            $program_handled = implode(',', $data['program_handled']); // Convert array to comma-separated string
        } else {
            $program_handled = $data['program_handled'];
        }

        // Check if year_handled is an array before using implode
        if (is_array($data['year_handled'])) {
            $year_handled = implode(',', $data['year_handled']); // Convert array to comma-separated string
        } else {
            $year_handled = $data['year_handled'];
        }

        $instructor_name = $data['instructor_name'];
        $admin_id = $data['admin_id'];

        // Check if the provided admin_id exists in the admin_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM admin_credentials_tbl WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // If admin_id doesn't exist, return error
        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Invalid admin ID. Cannot create instructor account.'
            ];
        }

        // Check if the instructor's email already exists in instructor_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM instructor_credentials_tbl WHERE instructor_email = ?");
        $stmt->execute([$instructor_email]);
        $existing_instructor = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the email already exists, return error
        if ($existing_instructor) {
            return [
                'success' => false,
                'message' => 'Instructor account with this email already exists.'
            ];
        }

        // Hash the password for security
        $hashed_password = password_hash($instructor_password, PASSWORD_DEFAULT);

        // Prepare SQL statement
        $stmt = $this->db->prepare("INSERT INTO instructor_credentials_tbl (instructor_email, instructor_password, role, block_handled, program_handled, instructor_name, year_handled) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind parameters and execute the statement
        $stmt->execute([$instructor_email, $hashed_password, $role, $block_handled, $program_handled, $instructor_name, $year_handled]);

        // Check if insertion was successful
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Instructor account created successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to create instructor account.'
            ];
        }
    }

    public function admin_login($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $stmt = $this->db->prepare("SELECT admin_id, admin_email, admin_password FROM admin_credentials_tbl WHERE admin_email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();
        if ($admin && $password === $admin['admin_password']) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $admin['admin_id'],
                "email" => $email,
                "role" => "admin",
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "token" => $jwt, "role" => "admin", "admin_id" => $admin['admin_id']);
        } else {
            return array("success" => false, "message" => "Invalid email or password");
        }
    }

    public function student_login($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $stmt = $this->db->prepare("SELECT user_id, student_email, student_password FROM student_credentials_tbl WHERE student_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['student_password'])) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $user['user_id'],
                "email" => $email,
                "role" => "student",
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "token" => $jwt, "role" => "student", "user_id" => $user['user_id']);
        } else {
            return array("success" => false, "message" => "Invalid email or password");
        }
    }

    public function instructor_login($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $stmt = $this->db->prepare("SELECT instructor_id, instructor_email, instructor_password FROM instructor_credentials_tbl WHERE instructor_email = ?");
        $stmt->execute([$email]);
        $instructor = $stmt->fetch();
        if ($instructor && password_verify($password, $instructor['instructor_password'])) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $instructor['instructor_id'],
                "email" => $email,
                "role" => "instructor",
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "token" => $jwt, "role" => "instructor", "instructor_id" => $instructor['instructor_id']);
        } else {
            return array("success" => false, "message" => "Invalid email or password");
        }
    }

    public function employer_login($data)
    {
        $email = $data['email'];
        $password = $data['password'];
        $stmt = $this->db->prepare("SELECT employer_id, employer_email, employer_password FROM employer_credentials_tbl WHERE employer_email = ?");
        $stmt->execute([$email]);
        $employer = $stmt->fetch();
        if ($employer && password_verify($password, $employer['employer_password'])) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $employer['employer_id'],
                "email" => $email,
                "role" => "employer",
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "token" => $jwt, "role" => "employer", "employer_id" => $employer['employer_id']);
        } else {
            return array("success" => false, "message" => "Invalid email or password");
        }
    }

    public function linkStudentAndEmployer($data)
    {
        $student_id = $data['student_id'];
        $employer_id = $data['employer_id'];
        $admin_id = $data['admin_id'];

        // Check if the provided admin_id exists in the admin_credentials_tbl
        $stmt = $this->db->prepare("SELECT * FROM admin_credentials_tbl WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // If admin_id doesn't exist, return error
        if (!$admin) {
            return [
                'success' => false,
                'message' => 'Invalid admin ID. Cannot create instructor account.'
            ];
        }

        // Prepare SQL statement
        $stmt = $this->db->prepare("INSERT INTO student_employer_relationship_tbl (student_id, employer_id) VALUES (?, ?)");

        // Bind parameters and execute the statement
        $stmt->execute([$student_id, $employer_id]);

        // Check if insertion was successful
        if ($stmt->rowCount() > 0) {
            return [
                'success' => true,
                'message' => 'Accounts linked successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to link accounts.'
            ];
        }
    }

    public function deleteStudent($userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_credentials_tbl WHERE user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Student deleted successfully",
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No student deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteInstructor($instructorId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM instructor_credentials_tbl WHERE instructor_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$instructorId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Instructor deleted successfully",
                    "instructor_id" => $instructorId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No instructor deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteEmployer($employerId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM employer_credentials_tbl WHERE employer_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employerId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Employer deleted successfully",
                    "employer_id" => $employerId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No employer deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteLinkedAccount($employerId)
    {
        try {
            // Prepare SQL query to delete the record
            $query = "DELETE FROM student_employer_relationship_tbl WHERE employer_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$employerId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Linked Accounts deleted successfully",
                    "employer_id" => $employerId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No linked accounts deleted"
                );
            }
        } catch (PDOException $e) {
            // Log or display detailed error message
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function updateInformation($data)
    {
        if (!isset($data['user_id']) || !isset($data['student_mobile_number']) || !isset($data['company_address'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        $user_id = $data['user_id'];
        $mobile_number = $data['student_mobile_number'];
        $company_address = $data['company_address'];

        $stmt = $this->db->prepare("UPDATE student_credentials_tbl SET student_mobile_number = ?, company_address = ? WHERE user_id = ?");
        $result = $stmt->execute([$mobile_number, $company_address, $user_id]);

        if ($result) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $user_id,
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "message" => "Mobile number and company address updated successfully", "token" => $jwt);
        } else {
            return array("success" => false, "message" => "Failed to update mobile number and company address");
        }
    }

    public function updateStudentMobileNumber($data)
    {
        if (!isset($data['user_id']) || !isset($data['student_mobile_number'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        $user_id = $data['user_id'];
        $mobile_number = $data['student_mobile_number'];

        $stmt = $this->db->prepare("UPDATE student_credentials_tbl SET student_mobile_number = ? WHERE user_id = ?");
        $result = $stmt->execute([$mobile_number, $user_id]);

        if ($result) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $user_id,
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "message" => "Mobile number updated successfully", "token" => $jwt);
        } else {
            return array("success" => false, "message" => "Failed to update mobile number");
        }
    }

    public function updateStudentCompanyAddress($data)
    {
        if (!isset($data['user_id']) || !isset($data['company_address'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        $user_id = $data['user_id'];
        $company_address = $data['company_address'];

        $stmt = $this->db->prepare("UPDATE student_credentials_tbl SET company_address = ? WHERE user_id = ?");
        $result = $stmt->execute([$company_address, $user_id]);

        if ($result) {
            $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
            $payload = array(
                "user_id" => $user_id,
                "iat" => time()
            );
            $jwt = JWT::encode($payload, $secret_key, 'HS256');
            return array("success" => true, "message" => "Company address updated successfully", "token" => $jwt);
        } else {
            return array("success" => false, "message" => "Failed to update company address");
        }
    }

    public function uploadSignedEndorsementLetter($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/signed_endorsement_letter/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_signed_endorsement_letter_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_signed_endorsement_letter_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            $stmt = $this->db->prepare("INSERT INTO student_signed_endorsement_letter_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Update endorsement_status in instructor_requirement_checking_tbl
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET endorsement_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update endorsement status.");
        }

        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadSignedMOA($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/signed_moa/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_signed_moa_letter_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_signed_moa_letter_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
            return array("success" => true, "message" => "File updated successfully");
        }
        $stmt = $this->db->prepare("INSERT INTO student_signed_moa_letter_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
        if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
            unlink($uploadFile);
            return array("success" => false, "message" => "Failed to insert file details into database.");
        }
        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadSignedAcceptanceLetter($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/signed_acceptance_letter/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_signed_acceptance_letter_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_signed_acceptance_letter_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            $stmt = $this->db->prepare("INSERT INTO student_signed_acceptance_letter_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Update acceptance_status in instructor_requirement_checking_tbl
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET acceptance_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update acceptance status.");
        }

        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadResume($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/resume/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_resume_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_resume_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            $stmt = $this->db->prepare("INSERT INTO student_resume_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Update resume_status in instructor_requirement_checking_tbl
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET resume_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update resume status.");
        }

        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadVaccinationCard($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/vaccination_card/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_vaccination_card_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_vaccination_card_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
            return array("success" => true, "message" => "File updated successfully");
        }
        $stmt = $this->db->prepare("INSERT INTO student_vaccination_card_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
        if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
            unlink($uploadFile);
            return array("success" => false, "message" => "Failed to insert file details into database.");
        }
        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadMedicalCertificate($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/student_medical_certificate/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_medical_certificate_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_medical_certificate_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
            return array("success" => true, "message" => "File updated successfully");
        }
        $stmt = $this->db->prepare("INSERT INTO student_medical_certificate_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
        if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
            unlink($uploadFile);
            return array("success" => false, "message" => "Failed to insert file details into database.");
        }
        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadBarangayClearance($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }
        $uploadDir = 'student_signed_documents/barangay_clearance/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            return array("success" => false, "message" => "Error executing database query.");
        }
        $stmt = $this->db->prepare("SELECT file_id FROM student_barangay_clearance_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_barangay_clearance_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile);
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
            return array("success" => true, "message" => "File updated successfully");
        }
        $stmt = $this->db->prepare("INSERT INTO student_barangay_clearance_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
        if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
            unlink($uploadFile);
            return array("success" => false, "message" => "Failed to insert file details into database.");
        }
        $fileId = $this->db->lastInsertId();
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadSignedApplicationLetter($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        $uploadDir = 'student_signed_documents/signed_application_letter/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a record already exists for the user
        $stmt = $this->db->prepare("SELECT file_id FROM student_signed_application_letter_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        // If a record exists, update the existing record
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_signed_application_letter_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile); // Delete the uploaded file if update fails
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            // If no record exists, insert a new record
            $stmt = $this->db->prepare("INSERT INTO student_signed_application_letter_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile); // Delete the uploaded file if insertion fails
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Update application_status in instructor_requirement_checking_tbl
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET application_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update application status.");
        }

        // Get the inserted file ID
        $fileId = $this->db->lastInsertId();

        // Generate JWT token
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        // Return success response with JWT token
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadSignedParentsConsentLetter($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        $uploadDir = 'student_signed_documents/signed_parents_consent_letter/';
        if (!is_dir($uploadDir)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadDir . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a record already exists for the user
        $stmt = $this->db->prepare("SELECT file_id FROM student_signed_parents_consent_letter_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        // If a record exists, update the existing record
        if ($existingRecord) {
            $stmt = $this->db->prepare("UPDATE student_signed_parents_consent_letter_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile); // Delete the uploaded file if update fails
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            // If no record exists, insert a new record
            $stmt = $this->db->prepare("INSERT INTO student_signed_parents_consent_letter_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile); // Delete the uploaded file if insertion fails
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Update consent_status in instructor_requirement_checking_tbl
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET consent_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update consent status.");
        }

        // Get the inserted file ID
        $fileId = $this->db->lastInsertId();

        // Generate JWT token
        $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
        $payload = array(
            "message" => "File uploaded successfully.",
            "filename" => $fileName,
            "file_id" => $fileId,
            "user_id" => $userId
        );
        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        // Return success response with JWT token
        return array("success" => true, "token" => $jwt, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function record_time_in($user_id)
    {
        try {
            // Get current timestamp
            $time_in = date('Y-m-d H:i:s');

            // Fetch the school_id based on the provided user_id
            $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
            if ($stmt->execute([$user_id])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $schoolId = $row['school_id'];
                } else {
                    // Handle case where no row is found
                    return array("success" => false, "message" => "No school_id found for the provided user_id.");
                }
            } else {
                // Handle query execution error
                return array("success" => false, "message" => "Error executing database query.");
            }

            // Set default status
            $status = "Unverified";

            // Insert record into student_dtr_tbl
            $query = "INSERT INTO student_dtr_tbl (time_in, user_id, school_id, dtr_status) VALUES (:time_in, :user_id, :school_id, :status)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':time_in', $time_in);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':school_id', $schoolId);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            // Generate JWT token
            $payload = array(
                "message" => "Time in recorded successfully",
                "token" => $this->generate_token($user_id),
                "user_id" => $user_id,
                "school_id" => $schoolId
            );

            // Encode payload into JWT token
            $jwt = JWT::encode($payload, $this->secret_key, 'HS256');

            return array("success" => true, "jwt" => $jwt);
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to record time in: " . $e->getMessage());

            // Send an error response
            http_response_code(500); // Internal Server Error
            echo json_encode(array("success" => false, "message" => "Failed to record time in: " . $e->getMessage()));
            exit;
        }
    }

    public function record_time_out($user_id)
    {
        try {
            // Get current timestamp
            $time_out = date('Y-m-d H:i:s');

            // Fetch the last time_in for the user
            $query = "SELECT time_in FROM student_dtr_tbl WHERE user_id = :user_id ORDER BY dtr_id DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $time_in = $result['time_in'];

                // Calculate hours worked
                $hours_worked = $this->calculate_hours_worked($time_in, $time_out);

                // Update time_out and hours_worked for the last record of the user
                $query = "UPDATE student_dtr_tbl SET time_out = :time_out, hours_worked = :hours_worked WHERE user_id = :user_id ORDER BY dtr_id DESC LIMIT 1";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':time_out', $time_out);
                $stmt->bindParam(':hours_worked', $hours_worked);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();

                // Generate JWT token
                $payload = array(
                    "message" => "Time out recorded successfully",
                    "token" => $this->generate_token($user_id),
                    "user_id" => $user_id
                );

                // Encode payload into JWT token
                $jwt = JWT::encode($payload, $this->secret_key, 'HS256');

                return array("success" => true, "jwt" => $jwt);
            } else {
                return array("success" => false, "message" => "No time in record found for the user.");
            }
        } catch (Exception $e) {
            return array("success" => false, "message" => "Failed to record time out: " . $e->getMessage());
        }
    }

    private function calculate_hours_worked($time_in, $time_out)
    {
        $datetime1 = new DateTime($time_in);
        $datetime2 = new DateTime($time_out);
        $interval = $datetime1->diff($datetime2);
        return $interval->h + ($interval->i / 60); // Returns hours as a decimal (hours + minutes/60)
    }

    public function uploadCCSPicture($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_proof_of_evidences/student_ccs_picture/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a file already exists for the user in this category
        $stmt = $this->db->prepare("SELECT file_id, file_path FROM student_ccs_picture_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingFile) {
                // If a file already exists, update the existing record and delete the old file
                $oldFilePath = $existingFile['file_path'];
                $stmt = $this->db->prepare("UPDATE student_ccs_picture_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE file_id = ?");
                if ($stmt->execute([$fileName, $uploadFile, $schoolId, $existingFile['file_id']])) {
                    unlink($oldFilePath); // Delete the old file
                    $fileId = $existingFile['file_id'];
                } else {
                    unlink($uploadFile); // Delete the uploaded file if update fails
                    return array("success" => false, "message" => "Failed to update file details in database.");
                }
            } else {
                // If no existing file, insert a new record
                $stmt = $this->db->prepare("INSERT INTO student_ccs_picture_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
                if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                    unlink($uploadFile); // Delete the uploaded file if insertion fails
                    return array("success" => false, "message" => "Failed to insert file details into database.");
                }
                $fileId = $this->db->lastInsertId();
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Update the instructor_requirement_tbl to set ccs_status to "Not Yet Cleared"
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET ccs_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update CCS status in instructor requirement table.");
        }

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadSeminarCertificate($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_proof_of_evidences/student_seminar_certificate/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a file already exists for the user in this category
        $stmt = $this->db->prepare("SELECT file_id, file_path FROM student_seminar_certificate_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingFile) {
                // If a file already exists, update the existing record and delete the old file
                $oldFilePath = $existingFile['file_path'];
                $stmt = $this->db->prepare("UPDATE student_seminar_certificate_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE file_id = ?");
                if ($stmt->execute([$fileName, $uploadFile, $schoolId, $existingFile['file_id']])) {
                    unlink($oldFilePath); // Delete the old file
                    $fileId = $existingFile['file_id'];
                } else {
                    unlink($uploadFile); // Delete the uploaded file if update fails
                    return array("success" => false, "message" => "Failed to update file details in database.");
                }
            } else {
                // If no existing file, insert a new record
                $stmt = $this->db->prepare("INSERT INTO student_seminar_certificate_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
                if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                    unlink($uploadFile); // Delete the uploaded file if insertion fails
                    return array("success" => false, "message" => "Failed to insert file details into database.");
                }
                $fileId = $this->db->lastInsertId();
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Update the instructor_requirement_tbl to set ccs_status to "Not Yet Cleared"
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET seminar_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update CCS status in instructor requirement table.");
        }

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadFinalReport($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_final_report/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a file already exists for the user in this category
        $stmt = $this->db->prepare("SELECT file_id, file_path FROM student_final_report_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingFile) {
                // If a file already exists, update the existing record and delete the old file
                $oldFilePath = $existingFile['file_path'];
                $stmt = $this->db->prepare("UPDATE student_final_report_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE file_id = ?");
                if ($stmt->execute([$fileName, $uploadFile, $schoolId, $existingFile['file_id']])) {
                    unlink($oldFilePath); // Delete the old file
                    $fileId = $existingFile['file_id'];
                } else {
                    unlink($uploadFile); // Delete the uploaded file if update fails
                    return array("success" => false, "message" => "Failed to update file details in database.");
                }
            } else {
                // If no existing file, insert a new record with default report_status as "Not Yet Cleared"
                $stmt = $this->db->prepare("INSERT INTO student_final_report_tbl (file_name, file_path, user_id, school_id, report_status) VALUES (?, ?, ?, ?, ?)");
                $defaultReportStatus = "Not Yet Cleared";
                if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId, $defaultReportStatus])) {
                    unlink($uploadFile); // Delete the uploaded file if insertion fails
                    return array("success" => false, "message" => "Failed to insert file details into database.");
                }
                $fileId = $this->db->lastInsertId();
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadSportsfestPicture($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_proof_of_evidences/student_sportsfest_picture/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a file already exists for the user in this category
        $stmt = $this->db->prepare("SELECT file_id, file_path FROM student_sportsfest_picture_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $existingFile = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingFile) {
                // If a file already exists, update the existing record and delete the old file
                $oldFilePath = $existingFile['file_path'];
                $stmt = $this->db->prepare("UPDATE student_sportsfest_picture_tbl SET file_name = ?, file_path = ?, school_id = ? WHERE file_id = ?");
                if ($stmt->execute([$fileName, $uploadFile, $schoolId, $existingFile['file_id']])) {
                    unlink($oldFilePath); // Delete the old file
                    $fileId = $existingFile['file_id'];
                } else {
                    unlink($uploadFile); // Delete the uploaded file if update fails
                    return array("success" => false, "message" => "Failed to update file details in database.");
                }
            } else {
                // If no existing file, insert a new record
                $stmt = $this->db->prepare("INSERT INTO student_sportsfest_picture_tbl (file_name, file_path, user_id, school_id) VALUES (?, ?, ?, ?)");
                if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                    unlink($uploadFile); // Delete the uploaded file if insertion fails
                    return array("success" => false, "message" => "Failed to insert file details into database.");
                }
                $fileId = $this->db->lastInsertId();
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Update the instructor_requirement_tbl to set ccs_status to "Not Yet Cleared"
        $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET sportsfest_status = 'Not Yet Cleared' WHERE student_id = ?");
        if (!$stmt->execute([$userId])) {
            return array("success" => false, "message" => "Failed to update CCS status in instructor requirement table.");
        }

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function deleteProofOfEvidenceFile($fileName, $category, $userId, $db)
    {
        // Define the directory where the files are stored based on the category
        $directory = '';
        $statusColumn = '';

        // Adjust the directory path and status column based on the category
        switch ($category) {
            case 'Activities Documentation':
                $directory = "student_proof_of_evidences/student_ccs_picture/";
                $tableName = 'student_ccs_picture_tbl';
                $statusColumn = 'ccs_status';
                break;
            case 'Trainings/Seminars':
                $directory = "student_proof_of_evidences/student_seminar_certificate/";
                $tableName = 'student_seminar_certificate_tbl';
                $statusColumn = 'seminar_status';
                break;
            case 'OJT Documentation':
                $directory = "student_proof_of_evidences/student_sportsfest_picture/";
                $tableName = 'student_sportsfest_picture_tbl';
                $statusColumn = 'sportsfest_status';
                break;
        }

        // Construct the full file path
        $filePath = $directory . $fileName;

        // Check if the file exists
        if (file_exists($filePath)) {
            // Attempt to delete the file
            if (unlink($filePath)) {
                // Delete the corresponding entry from the database
                $stmt = $db->prepare("DELETE FROM $tableName WHERE user_id = ? AND file_name = ?");
                $stmt->execute([$userId, $fileName]);

                // Update the status in instructor_requirement_checking_tbl
                if ($statusColumn) {
                    $stmt = $db->prepare("UPDATE instructor_requirement_checking_tbl SET $statusColumn = 'Not Yet Cleared' WHERE student_id = ?");
                    $stmt->execute([$userId]);
                }

                // Return the success message along with file details
                return array("success" => true, "message" => "File deleted successfully.", "file_name" => $fileName, "category" => $category, "user_id" => $userId);
            } else {
                return array("success" => false, "message" => "Failed to delete file.");
            }
        } else {
            return array("success" => false, "message" => "File not found.");
        }
    }

    public function insertDailyAccomplishment($data)
    {
        // Check if the required fields are present in the data
        if (!isset($data['description']) || !isset($data['start_time']) || !isset($data['end_time']) || !isset($data['number_of_hours']) || !isset($data['date']) || !isset($data['user_id'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        // Extract the data
        $description = $data['description'];
        $start_time = $data['start_time'];
        $end_time = $data['end_time'];
        $number_of_hours = $data['number_of_hours'];
        $date = $data['date'];
        $user_id = $data['user_id'];

        // Set default value for accomplishment_status
        $accomplishment_status = 'Unverified';

        try {
            // Fetch the school_id based on the provided user_id
            $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
            if ($stmt->execute([$user_id])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $schoolId = $row['school_id'];
                } else {
                    // Handle case where no row is found
                    return array("success" => false, "message" => "No school_id found for the provided user_id.");
                }
            } else {
                // Handle query execution error
                return array("success" => false, "message" => "Error executing database query.");
            }

            // Prepare SQL statement
            $stmt = $this->db->prepare("INSERT INTO student_daily_accomplishments_tbl (description_of_activities, start_time, end_time, number_of_hours, date, user_id, school_id, accomplishment_status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            // Execute the statement
            $result = $stmt->execute([$description, $start_time, $end_time, $number_of_hours, $date, $user_id, $schoolId, $accomplishment_status]);

            if ($result) {
                // Generate JWT token with user_id in payload
                $secret_key = "ee9150bd81968d68bd081a746a548719bbd66eba8f1945711b6daf4790005923";
                $payload = array(
                    "user_id" => $user_id,
                    "iat" => time()
                );
                $jwt = JWT::encode($payload, $secret_key, 'HS256');

                // Return JSON response with token
                return array("success" => true, "message" => "Daily accomplishment inserted successfully", "token" => $jwt, "user_id" => $user_id, "school_id" => $schoolId);
            } else {
                return array("success" => false, "message" => "Failed to insert daily accomplishment");
            }
        } catch (PDOException $e) {
            return array("success" => false, "message" => "Failed to insert daily accomplishment: " . $e->getMessage());
        }
    }

    public function deleteDailyAccomplishment($dailyAccomplishmentId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_daily_accomplishments_tbl WHERE daily_accomplishments_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$dailyAccomplishmentId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Daily accomplishment deleted successfully",
                    "daily_accomplishments_id" => $dailyAccomplishmentId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No daily accomplishment deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteSkills($portfolioSkillsId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_portfolio_skills_tbl WHERE portfolio_skills_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$portfolioSkillsId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "portfolio_skills_id" => $portfolioSkillsId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteDTR($dtrId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_dtr_tbl WHERE dtr_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$dtrId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "dtr_id" => $dtrId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteCertificate($fileId, $employerId, $studentId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM certificate_of_completion_tbl WHERE file_id = ? AND employer_id = ? AND student_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$fileId, $employerId, $studentId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "file_id" => $fileId,
                    "employer_id" => $employerId,
                    "student_id" => $studentId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteEducation($portfolioEducationId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_portfolio_education_tbl WHERE portfolio_education_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$portfolioEducationId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "portfolio_education_id" => $portfolioEducationId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function insertSkillsRecord($data)
    {
        try {
            // Extract data from the request
            $skills = $data['skills'];
            $user_id = $data['user_id'];

            // Fetch the school_id based on the provided user_id
            $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
            if ($stmt->execute([$user_id])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $schoolId = $row['school_id'];
                } else {
                    // Handle case where no row is found
                    return array("success" => false, "message" => "No school_id found for the provided user_id.");
                }
            } else {
                // Handle query execution error
                return array("success" => false, "message" => "Error executing database query.");
            }

            // Log extracted data for debugging
            error_log('Skills: ' . json_encode($skills));
            error_log('User ID: ' . $user_id);

            // Check if $skills is an array
            if (!is_array($skills)) {
                throw new Exception('Skills must be provided as an array');
            }

            // Prepare arrays to store skills and proficiencies separately
            $skillNames = [];
            $proficiencies = [];

            // Loop through the skills array to separate names and proficiencies
            foreach ($skills as $skill) {
                $skillNames[] = $skill['name'];
                // If proficiency is not provided, set it to null
                $proficiencies[] = isset($skill['proficiency']) ? $skill['proficiency'] : null;
            }

            // Encode skills and proficiencies arrays as JSON strings
            $skillsJson = json_encode($skillNames);
            $proficienciesJson = json_encode($proficiencies);

            // Prepare SQL query to insert the record into student_portfolio_skills_tbl
            $query = "INSERT INTO student_portfolio_skills_tbl (skills, proficiency, user_id, school_id) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$skillsJson, $proficienciesJson, $user_id, $schoolId]);

            // Check if the insertion was successful
            if ($stmt->rowCount() > 0) {
                // Insertion successful
                return array(
                    "success" => true,
                    "message" => "Skills record inserted successfully",
                    "token" => $this->generate_token($user_id),
                    "user_id" => $user_id,
                    "school_id" => $schoolId,
                    "skills" => $skillNames,
                    "proficiency" => $proficiencies
                );
            } else {
                // No record inserted
                return array(
                    "success" => false,
                    "message" => "Failed to insert skills record"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        } catch (Exception $e) {
            // Handle other errors
            return array(
                "success" => false,
                "message" => $e->getMessage()
            );
        }
    }

    public function insertEducationRecord($data)
    {
        try {
            // Extract data from the request
            $education = $data['education'];
            $school = $data['school'];
            $year = $data['year'];
            $user_id = $data['user_id'];

            // Fetch the school_id based on the provided user_id
            $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
            if ($stmt->execute([$user_id])) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $schoolId = $row['school_id'];
                } else {
                    // Handle case where no row is found
                    return array("success" => false, "message" => "No school_id found for the provided user_id.");
                }
            } else {
                // Handle query execution error
                return array("success" => false, "message" => "Error executing database query.");
            }

            // Log extracted data for debugging
            error_log('Education: ' . $education);
            error_log('School: ' . $school);
            error_log('Year: ' . $year);
            error_log('User ID: ' . $user_id);

            // Prepare SQL query to insert the record into student_portfolio_education_tbl
            $query = "INSERT INTO student_portfolio_education_tbl (education, school, year, user_id, school_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$education, $school, $year, $user_id, $schoolId]);

            // Check if the insertion was successful
            if ($stmt->rowCount() > 0) {
                // Insertion successful
                return array(
                    "success" => true,
                    "message" => "Education record inserted successfully",
                    "token" => $this->generate_token($user_id),
                    "user_id" => $user_id,
                    "school_id" => $schoolId,
                    "education" => $education,
                    "school" => $school,
                    "year" => $year
                );
            } else {
                // No record inserted
                return array(
                    "success" => false,
                    "message" => "Failed to insert education record"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        } catch (Exception $e) {
            // Handle other errors
            return array(
                "success" => false,
                "message" => $e->getMessage()
            );
        }
    }

    public function uploadUserPhoto($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_profile_photo/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if the user already has a profile picture
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM student_profile_picture_tbl WHERE user_id = ?");
        $stmt->execute([$userId]);
        $profileExists = $stmt->fetchColumn();

        // Insert or update file details into the database
        if ($profileExists) {
            // Update existing profile picture record
            $stmt = $this->db->prepare("UPDATE student_profile_picture_tbl SET file_name = ?, image_path = ?, timestamp = NOW(), school_id = ? WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile); // Delete the uploaded file if update fails
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            // Insert new profile picture record
            $stmt = $this->db->prepare("INSERT INTO student_profile_picture_tbl (file_name, image_path, user_id, timestamp, school_id) VALUES (?, ?, ?, NOW(), ?)");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile); // Delete the uploaded file if insertion fails
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Return success response
        return array("success" => true, "message" => "File uploaded successfully", "user_id" => $userId, "school_id" => $schoolId);
    }

    public function updateDTRStatus($dtr, $employerId, $studentId)
    {
        try {
            // Validate input
            if (empty($dtr['dtr_id']) || empty($dtr['status'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $dtr_id = $dtr['dtr_id'];
            $status = $dtr['status'];

            // Validate status
            if (!in_array($status, ["Approved", "Rejected"])) {
                return ["success" => false, "message" => "Invalid status provided."];
            }

            // Prepare and execute the query
            $query = "UPDATE student_dtr_tbl dtr
                  INNER JOIN student_employer_relationship_tbl rel ON dtr.user_id = rel.student_id
                  SET dtr.dtr_status = ?
                  WHERE dtr.dtr_id = ? AND rel.employer_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $dtr_id, $employerId, $studentId]);

            return ["success" => $success, "message" => $success ? "DTR status updated successfully." : "Failed to update DTR status."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update DTR status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update DTR status: " . $e->getMessage()];
        }
    }

    public function updateOJTStatus($instructorId, $studentId, $status)
    {
        try {
            // Validate input
            if (empty($instructorId) || empty($studentId) || empty($status)) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            // Prepare and execute the query
            $query = "UPDATE student_credentials_tbl dtr
                  INNER JOIN student_instructor_relationship_tbl rel ON dtr.user_id = rel.student_id
                  SET dtr.ojt_status = ?
                  WHERE rel.instructor_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $instructorId, $studentId]);

            return ["success" => $success, "message" => $success ? "OJT status updated successfully." : "Failed to update OJT status."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update DTR status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update DTR status: " . $e->getMessage()];
        }
    }

    public function updateDTRFileStatus($dtr, $employerId, $studentId)
    {
        try {
            // Validate input
            if (empty($dtr['file_id']) || empty($dtr['status'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $file_id = $dtr['file_id'];
            $status = $dtr['status'];

            // Validate status
            if (!in_array($status, ["Approved", "Rejected"])) {
                return ["success" => false, "message" => "Invalid status provided."];
            }

            // Prepare and execute the query
            $query = "UPDATE student_file_dtr_tbl dtr
                  INNER JOIN student_employer_relationship_tbl rel ON dtr.user_id = rel.student_id
                  SET dtr.dtr_status = ?
                  WHERE dtr.file_id = ? AND rel.employer_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $file_id, $employerId, $studentId]);

            return ["success" => $success, "message" => $success ? "DTR status updated successfully." : "Failed to update DTR status."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update DTR status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update DTR status: " . $e->getMessage()];
        }
    }

    public function updateWeeklyAccomplishmentsFileStatus($weeklyAccomplishments, $employerId, $studentId)
    {
        try {
            // Validate input
            if (empty($weeklyAccomplishments['file_id']) || empty($weeklyAccomplishments['status'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $file_id = $weeklyAccomplishments['file_id'];
            $status = $weeklyAccomplishments['status'];

            // Validate status
            if (!in_array($status, ["Approved", "Rejected"])) {
                return ["success" => false, "message" => "Invalid status provided."];
            }

            // Prepare and execute the query
            $query = "UPDATE student_weekly_accomplishments_tbl dtr
                  INNER JOIN student_employer_relationship_tbl rel ON dtr.user_id = rel.student_id
                  SET dtr.weekly_status = ?
                  WHERE dtr.file_id = ? AND rel.employer_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $file_id, $employerId, $studentId]);
            return ["success" => $success, "message" => $success ? "Weekly accomplishments status updated successfully." : "Failed to update weekly accomplishments status."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update weekly accomplishments status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update weekly accomplishments status: " . $e->getMessage()];
        }
    }

    public function updateFinalReportStatus($report, $instructorId, $studentId)
    {
        try {
            // Validate input
            if (empty($report['file_id']) || empty($report['status'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $file_id = $report['file_id'];
            $status = $report['status'];

            // Validate status
            if (!in_array($status, ["Cleared", "Not Cleared", "Currently Verifying"])) {
                return ["success" => false, "message" => "Invalid status provided."];
            }

            // Prepare and execute the query
            $query = "UPDATE student_final_report_tbl dtr
                 INNER JOIN student_instructor_relationship_tbl rel ON dtr.user_id = rel.student_id
                 SET dtr.report_status = ?
                 WHERE dtr.file_id = ? AND rel.instructor_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $file_id, $instructorId, $studentId]);
            return ["success" => $success, "message" => $success ? "Final report status updated successfully." : "Failed to update final report status."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update DTR status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update DTR status: " . $e->getMessage()];
        }
    }

    public function updateDocumentationStatus($documentation, $instructorId, $studentId)
    {
        try {
            // Validate input
            if (empty($documentation['file_id']) || empty($documentation['status'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $file_id = $documentation['file_id'];
            $status = $documentation['status'];

            // Validate status
            if (!in_array($status, ["Cleared", "Not Cleared", "Currently Verifying"])) {
                return ["success" => false, "message" => "Invalid status provided."];
            }

            // Prepare and execute the query
            $query = "UPDATE student_documentation_tbl dtr
                 INNER JOIN student_instructor_relationship_tbl rel ON dtr.user_id = rel.student_id
                 SET dtr.documentation_status = ?
                 WHERE dtr.file_id = ? AND rel.instructor_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $file_id, $instructorId, $studentId]);
            return ["success" => $success, "message" => $success ? "Documentation status updated successfully." : "Failed to update documentation status."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update documentation status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update documentation status: " . $e->getMessage()];
        }
    }

    public function updateAccomplishmentStatus($accomplishment, $employerId, $studentId)
    {
        try {
            // Validate required parameters
            if (empty($accomplishment['daily_accomplishments_id']) || empty($accomplishment['status'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $accomplishmentId = $accomplishment['daily_accomplishments_id'];
            $status = $accomplishment['status'];

            // Validate status
            if (!in_array($status, ["Approved", "Rejected"])) {
                return ["success" => false, "message" => "Invalid status provided."];
            }

            // Prepare and execute the query
            $query = "
            UPDATE student_daily_accomplishments_tbl a
            INNER JOIN student_employer_relationship_tbl r ON a.user_id = r.student_id
            SET a.accomplishment_status = ?
            WHERE a.daily_accomplishments_id = ? AND r.employer_id = ? AND a.user_id = ?
        ";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$status, $accomplishmentId, $employerId, $studentId]);

            // Return the result
            if ($success) {
                return ["success" => true, "message" => "Accomplishment status updated successfully."];
            } else {
                return ["success" => false, "message" => "Failed to update accomplishment status."];
            }
        } catch (Exception $e) {
            // Log and return the error
            error_log("Failed to update accomplishment status: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update accomplishment status: " . $e->getMessage()];
        }
    }

    public function insertEmployerFeedback($data)
    {
        // Extract data from the $data array
        $employer_id = $data['employer_id'];
        $student_name = $data['student_name'];
        $office_department_branch = $data['office_department_branch'];
        $supervisor = $data['supervisor'];
        $hours_worked = $data['hours_worked'];
        $student_id = $data['student_id'];
        $knowledge_criteria_1 = $data['knowledge_criteria_1'];
        $knowledge_criteria_2 = $data['knowledge_criteria_2'];
        $knowledge_criteria_3 = $data['knowledge_criteria_3'];
        $knowledge_criteria_4 = $data['knowledge_criteria_4'];
        $knowledge_criteria_5 = $data['knowledge_criteria_5'];
        $skills_criteria_1 = $data['skills_criteria_1'];
        $skills_criteria_2 = $data['skills_criteria_2'];
        $skills_criteria_3 = $data['skills_criteria_3'];
        $skills_criteria_4 = $data['skills_criteria_4'];
        $skills_criteria_5 = $data['skills_criteria_5'];
        $skills_criteria_6 = $data['skills_criteria_6'];
        $skills_criteria_7 = $data['skills_criteria_7'];
        $skills_criteria_8 = $data['skills_criteria_8'];
        $attitude_criteria_1 = $data['attitude_criteria_1'];
        $attitude_criteria_2 = $data['attitude_criteria_2'];
        $attitude_criteria_3 = $data['attitude_criteria_3'];
        $attitude_criteria_4 = $data['attitude_criteria_4'];
        $attitude_criteria_5 = $data['attitude_criteria_5'];
        $attitude_criteria_6 = $data['attitude_criteria_6'];
        $attitude_criteria_7 = $data['attitude_criteria_7'];
        $attitude_criteria_8 = $data['attitude_criteria_8'];
        $attitude_criteria_9 = $data['attitude_criteria_9'];
        $attitude_criteria_10 = $data['attitude_criteria_10'];
        $attitude_criteria_11 = $data['attitude_criteria_11'];
        $attitude_criteria_12 = $data['attitude_criteria_12'];
        $attitude_criteria_13 = $data['attitude_criteria_13'];
        $overall_performance = $data['overall_performance'];
        $major_strongpoints = $data['major_strongpoints'];
        $major_weakpoints = $data['major_weakpoints'];
        $other_comments = $data['other_comments'];
        $suggestions_strongpoints = $data['suggestions_strongpoints'];
        $suggestions_weakpoints = $data['suggestions_weakpoints'];
        $recommendation = $data['recommendation'];

        // Calculate the scores
        $knowledge_score = $knowledge_criteria_1 + $knowledge_criteria_2 + $knowledge_criteria_3 + $knowledge_criteria_4 + $knowledge_criteria_5;
        $skills_score = $skills_criteria_1 + $skills_criteria_2 + $skills_criteria_3 + $skills_criteria_4 + $skills_criteria_5 + $skills_criteria_6 + $skills_criteria_7 + $skills_criteria_8;
        $attitude_score = $attitude_criteria_1 + $attitude_criteria_2 + $attitude_criteria_3 + $attitude_criteria_4 + $attitude_criteria_5 + $attitude_criteria_6 + $attitude_criteria_7 + $attitude_criteria_8 + $attitude_criteria_9 + $attitude_criteria_10 + $attitude_criteria_11 + $attitude_criteria_12 + $attitude_criteria_13;

        try {
            // Prepare SQL statement
            $stmt = $this->db->prepare("INSERT INTO employer_feedback_tbl 
        (employer_id, student_name, office_department_branch, supervisor, hours_worked, student_id,
        knowledge_criteria_1, knowledge_criteria_2, knowledge_criteria_3, knowledge_criteria_4, knowledge_criteria_5,
        skills_criteria_1, skills_criteria_2, skills_criteria_3, skills_criteria_4, skills_criteria_5,
        skills_criteria_6, skills_criteria_7, skills_criteria_8,
        attitude_criteria_1, attitude_criteria_2, attitude_criteria_3, attitude_criteria_4, attitude_criteria_5,
        attitude_criteria_6, attitude_criteria_7, attitude_criteria_8, attitude_criteria_9, attitude_criteria_10,
        attitude_criteria_11, attitude_criteria_12, attitude_criteria_13,
        overall_performance, major_strongpoints, major_weakpoints, other_comments, suggestions_strongpoints,
        suggestions_weakpoints, recommendation,
        knowledge_score, skills_score, attitude_score) 
        VALUES 
        (:employer_id, :student_name, :office_department_branch, :supervisor, :hours_worked, :student_id,
        :knowledge_criteria_1, :knowledge_criteria_2, :knowledge_criteria_3, :knowledge_criteria_4, :knowledge_criteria_5,
        :skills_criteria_1, :skills_criteria_2, :skills_criteria_3, :skills_criteria_4, :skills_criteria_5,
        :skills_criteria_6, :skills_criteria_7, :skills_criteria_8,
        :attitude_criteria_1, :attitude_criteria_2, :attitude_criteria_3, :attitude_criteria_4, :attitude_criteria_5,
        :attitude_criteria_6, :attitude_criteria_7, :attitude_criteria_8, :attitude_criteria_9, :attitude_criteria_10,
        :attitude_criteria_11, :attitude_criteria_12, :attitude_criteria_13,
        :overall_performance, :major_strongpoints, :major_weakpoints, :other_comments, :suggestions_strongpoints,
        :suggestions_weakpoints, :recommendation,
        :knowledge_score, :skills_score, :attitude_score)");

            // Bind parameters
            $stmt->bindParam(':employer_id', $employer_id);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':office_department_branch', $office_department_branch);
            $stmt->bindParam(':supervisor', $supervisor);
            $stmt->bindParam(':hours_worked', $hours_worked);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':knowledge_criteria_1', $knowledge_criteria_1);
            $stmt->bindParam(':knowledge_criteria_2', $knowledge_criteria_2);
            $stmt->bindParam(':knowledge_criteria_3', $knowledge_criteria_3);
            $stmt->bindParam(':knowledge_criteria_4', $knowledge_criteria_4);
            $stmt->bindParam(':knowledge_criteria_5', $knowledge_criteria_5);
            $stmt->bindParam(':skills_criteria_1', $skills_criteria_1);
            $stmt->bindParam(':skills_criteria_2', $skills_criteria_2);
            $stmt->bindParam(':skills_criteria_3', $skills_criteria_3);
            $stmt->bindParam(':skills_criteria_4', $skills_criteria_4);
            $stmt->bindParam(':skills_criteria_5', $skills_criteria_5);
            $stmt->bindParam(':skills_criteria_6', $skills_criteria_6);
            $stmt->bindParam(':skills_criteria_7', $skills_criteria_7);
            $stmt->bindParam(':skills_criteria_8', $skills_criteria_8);
            $stmt->bindParam(':attitude_criteria_1', $attitude_criteria_1);
            $stmt->bindParam(':attitude_criteria_2', $attitude_criteria_2);
            $stmt->bindParam(':attitude_criteria_3', $attitude_criteria_3);
            $stmt->bindParam(':attitude_criteria_4', $attitude_criteria_4);
            $stmt->bindParam(':attitude_criteria_5', $attitude_criteria_5);
            $stmt->bindParam(':attitude_criteria_6', $attitude_criteria_6);
            $stmt->bindParam(':attitude_criteria_7', $attitude_criteria_7);
            $stmt->bindParam(':attitude_criteria_8', $attitude_criteria_8);
            $stmt->bindParam(':attitude_criteria_9', $attitude_criteria_9);
            $stmt->bindParam(':attitude_criteria_10', $attitude_criteria_10);
            $stmt->bindParam(':attitude_criteria_11', $attitude_criteria_11);
            $stmt->bindParam(':attitude_criteria_12', $attitude_criteria_12);
            $stmt->bindParam(':attitude_criteria_13', $attitude_criteria_13);
            $stmt->bindParam(':overall_performance', $overall_performance);
            $stmt->bindParam(':major_strongpoints', $major_strongpoints);
            $stmt->bindParam(':major_weakpoints', $major_weakpoints);
            $stmt->bindParam(':other_comments', $other_comments);
            $stmt->bindParam(':suggestions_strongpoints', $suggestions_strongpoints);
            $stmt->bindParam(':suggestions_weakpoints', $suggestions_weakpoints);
            $stmt->bindParam(':recommendation', $recommendation);
            $stmt->bindParam(':knowledge_score', $knowledge_score);
            $stmt->bindParam(':skills_score', $skills_score);
            $stmt->bindParam(':attitude_score', $attitude_score);

            // Execute the statement
            $stmt->execute();

            // Check if a row was inserted
            if ($stmt->rowCount() > 0) {
                // Insertion successful
                return array("success" => true, "message" => "Feedback inserted successfully");
            } else {
                // No rows inserted
                return array("success" => false, "message" => "Failed to insert feedback");
            }
        } catch (PDOException $e) {
            // Handle database errors
            // For example, you might log the error or return false
            error_log("Failed to insert feedback: " . $e->getMessage());

            // Send an error response
            http_response_code(500); // Internal Server Error
            return array("success" => false, "message" => "Failed to insert feedback: " . $e->getMessage());
        }
    }

    public function uploadCertificate($employerId, $studentId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Directory to store uploaded certificates
        $uploadDirectory = "certificate_of_completion/";

        // Check if file was uploaded successfully
        if (!isset($_FILES['certificate']) || $_FILES['certificate']['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Get the original file name
        $fileName = basename($_FILES['certificate']['name']);

        // Set the target file path
        $targetFile = $uploadDirectory . $fileName;

        // Check if certificate already exists for the given studentId and employerId
        $existingCertificate = $this->db->prepare("SELECT * FROM certificate_of_completion_tbl WHERE employer_id = ? AND student_id = ?");
        $existingCertificate->execute([$employerId, $studentId]);
        $certificateExists = $existingCertificate->fetch();

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['certificate']['tmp_name'], $targetFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        if ($certificateExists) {
            // If certificate already exists, update the file details
            $stmt = $this->db->prepare("UPDATE certificate_of_completion_tbl SET file_name = ?, file_path = ? WHERE employer_id = ? AND student_id = ?");
            if (!$stmt->execute([$fileName, $targetFile, $employerId, $studentId])) {
                unlink($targetFile); // Delete the uploaded file if database update fails
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }
        } else {
            // If certificate doesn't exist, insert file details into the database
            $stmt = $this->db->prepare("INSERT INTO certificate_of_completion_tbl (file_name, file_path, employer_id, student_id) VALUES (?, ?, ?, ?)");
            if (!$stmt->execute([$fileName, $targetFile, $employerId, $studentId])) {
                unlink($targetFile); // Delete the uploaded file if database insertion fails
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        return array("success" => true, "message" => "Certificate uploaded successfully.");
    }

    public function updateRequirementsStatus($studentId, $instructorId, $statusUpdates)
    {
        // Check if a record already exists for the student_id and instructor_id
        $stmt = $this->db->prepare("SELECT * FROM instructor_requirement_checking_tbl WHERE student_id = ? AND instructor_id = ?");
        $stmt->execute([$studentId, $instructorId]);
        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // If a record exists, update the specified columns
            $setColumns = "";
            $params = [];
            foreach ($statusUpdates as $column => $value) {
                $setColumns .= "$column = ?, ";
                $params[] = $value; // Collect parameter values
            }
            $setColumns = rtrim($setColumns, ", ");

            $stmt = $this->db->prepare("UPDATE instructor_requirement_checking_tbl SET $setColumns WHERE student_id = ? AND instructor_id = ?");
            $params[] = $studentId; // Append student_id to parameters
            $params[] = $instructorId; // Append instructor_id to parameters
            $stmt->execute($params); // Bind all parameters at once

            $payload = array(
                "message" => "Requirement statuses updated successfully.",
                "updated_statuses" => $statusUpdates,
                "student_id" => $studentId,
                "instructor_id" => $instructorId
            );
            return array("success" => true, "payload" => $payload, "message" => "Requirement statuses updated successfully.");
        } else {
            // If no record exists, insert a new record with the specified statuses
            $columns = array_keys($statusUpdates);
            $columnNames = implode(", ", $columns);
            $columnPlaceholders = rtrim(str_repeat("?, ", count($columns)), ", ");
            $values = array_values($statusUpdates);

            $stmt = $this->db->prepare("INSERT INTO instructor_requirement_checking_tbl ($columnNames, student_id, instructor_id) VALUES ($columnPlaceholders, ?, ?)");
            $values[] = $studentId; // Append student_id to values
            $values[] = $instructorId; // Append instructor_id to values
            $stmt->execute($values); // Bind all values at once

            $payload = array(
                "message" => "New requirement statuses inserted successfully.",
                "new_statuses" => $statusUpdates,
                "student_id" => $studentId,
                "instructor_id" => $instructorId
            );
            return array("success" => true, "payload" => $payload, "message" => "New requirement statuses inserted successfully.");
        }
    }

    public function makeAnnouncement($data)
    {
        $instructor_id = $data['instructor_id'];
        $title = $data['title'];
        $body = $data['body'];

        try {
            $stmt = $this->db->prepare("INSERT INTO instructor_announcement_tbl
            (instructor_id, title, body)
            VALUES
            (:instructor_id, :title, :body)");

            $stmt->bindParam(':instructor_id', $instructor_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':body', $body);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return array("success" => true, "message" => "Announcement created successfully");
            } else {
                return array("success" => false, "message" => "Announcement failed to be created");
            }
        } catch (PDOException $e) {
            error_log("Failed to create announcement: " . $e->getMessage());
            http_response_code(500);
            return array("success" => false, "message" => "Failed to create acnnouncement: " . $e->getMessage());
        }
    }

    public function deleteAnnouncement($instructorId, $announcementId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM instructor_announcement_tbl WHERE instructor_id = ? AND announcement_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$instructorId, $announcementId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "announcement_id" => $announcementId,
                    "instructor_id" => $instructorId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function updateDTRRemarks($data)
    {
        try {
            // Validate input
            if (empty($data['dtr_id']) || empty($data['student_id']) || empty($data['employer_id']) || !isset($data['remarks'])) {
                return ["success" => false, "message" => "Missing required parameters."];
            }

            $dtrId = $data['dtr_id'];
            $studentId = $data['student_id'];
            $employerId = $data['employer_id'];
            $remarks = $data['remarks']; // This can be an empty string

            // Prepare SQL query to update the remarks
            $query = "UPDATE student_dtr_tbl dtr
                  INNER JOIN student_employer_relationship_tbl rel ON dtr.user_id = rel.student_id
                  SET dtr.remarks = ?
                  WHERE dtr.dtr_id = ? AND rel.employer_id = ? AND rel.student_id = ?";
            $stmt = $this->db->prepare($query);
            $success = $stmt->execute([$remarks, $dtrId, $employerId, $studentId]);

            return ["success" => $success, "message" => $success ? "Remarks updated successfully." : "Failed to update remarks."];
        } catch (Exception $e) {
            // Log the error
            error_log("Failed to update remarks: " . $e->getMessage());
            http_response_code(500); // Internal Server Error
            return ["success" => false, "message" => "Failed to update remarks: " . $e->getMessage()];
        }
    }

    public function uploadDTR($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_dtr/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $originalFileName = basename($_FILES[$fileInputName]['name']);
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        $fileName = uniqid('dtr_') . '.' . $fileExtension; // Generate a unique filename
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Insert a new record for the uploaded file
        $stmt = $this->db->prepare("INSERT INTO student_file_dtr_tbl (file_name, file_path, user_id, school_id, dtr_status) VALUES (?, ?, ?, ?, 'Unverified')");
        if (!$stmt->execute([$originalFileName, $uploadFile, $userId, $schoolId])) {
            unlink($uploadFile); // Delete the uploaded file if insertion fails
            return array("success" => false, "message" => "Failed to insert file details into database.");
        }
        $fileId = $this->db->lastInsertId();

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadWeeklyAccomplishments($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_weekly_accomplishments/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Insert a new record for the uploaded file
        $stmt = $this->db->prepare("INSERT INTO student_weekly_accomplishments_tbl (file_name, file_path, user_id, school_id, weekly_status) VALUES (?, ?, ?, ?, 'Unverified')");
        if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
            unlink($uploadFile); // Delete the uploaded file if insertion fails
            return array("success" => false, "message" => "Failed to insert file details into database.");
        }
        $fileId = $this->db->lastInsertId();

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "file_id" => $fileId, "user_id" => $userId, "school_id" => $schoolId);
    }

    public function uploadDocumentation($fileInputName, $userId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return array("success" => false, "message" => "Invalid request method.");
        }

        // Declare the upload directory path
        $uploadPath = "student_documentation/";

        // Check if the file was uploaded
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
            return array("success" => false, "message" => "No file uploaded or upload error occurred.");
        }

        // Check if the upload directory exists
        if (!is_dir($uploadPath)) {
            return array("success" => false, "message" => "Upload directory does not exist.");
        }

        // Get file details
        $fileName = basename($_FILES[$fileInputName]['name']);
        $uploadFile = $uploadPath . $fileName;

        // Move the uploaded file to the upload directory
        if (!move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $uploadFile)) {
            return array("success" => false, "message" => "Failed to move uploaded file.");
        }

        // Fetch the school_id based on the provided user_id
        $stmt = $this->db->prepare("SELECT school_id FROM student_credentials_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $schoolId = $row['school_id'];
            } else {
                // Handle case where no row is found
                return array("success" => false, "message" => "No school_id found for the provided user_id.");
            }
        } else {
            // Handle query execution error
            return array("success" => false, "message" => "Error executing database query.");
        }

        // Check if a record already exists for the user
        $stmt = $this->db->prepare("SELECT file_path FROM student_documentation_tbl WHERE user_id = ?");
        if ($stmt->execute([$userId])) {
            $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            return array("success" => false, "message" => "Error checking existing documentation.");
        }

        if ($existingRecord) {
            // If a record exists, update it
            $existingFilePath = $existingRecord['file_path'];

            $stmt = $this->db->prepare("UPDATE student_documentation_tbl SET file_name = ?, file_path = ?, school_id = ?, documentation_status = 'Not Yet Cleared' WHERE user_id = ?");
            if (!$stmt->execute([$fileName, $uploadFile, $schoolId, $userId])) {
                unlink($uploadFile); // Delete the uploaded file if update fails
                return array("success" => false, "message" => "Failed to update file details in the database.");
            }

            // Delete the old file if the update is successful
            if (file_exists($existingFilePath)) {
                unlink($existingFilePath);
            }
        } else {
            // If no record exists, insert a new one
            $stmt = $this->db->prepare("INSERT INTO student_documentation_tbl (file_name, file_path, user_id, school_id, documentation_status) VALUES (?, ?, ?, ?, 'Not Yet Cleared')");
            if (!$stmt->execute([$fileName, $uploadFile, $userId, $schoolId])) {
                unlink($uploadFile); // Delete the uploaded file if insertion fails
                return array("success" => false, "message" => "Failed to insert file details into database.");
            }
        }

        // Generate JWT token
        $token = $this->generate_token($userId);

        // Return success response with JWT token
        return array("success" => true, "token" => $token, "message" => "File uploaded successfully", "user_id" => $userId, "school_id" => $schoolId);
    }

    public function deleteDTRFile($fileId, $studentId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_file_dtr_tbl WHERE file_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$fileId, $studentId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "file_id" => $fileId,
                    "student_id" => $studentId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteFinalReport($fileId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_final_report_tbl WHERE file_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$fileId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "file_id" => $fileId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteWeeklyAccomplishments($fileId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_weekly_accomplishments_tbl WHERE file_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$fileId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "file_id" => $fileId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function deleteDocumentation($fileId, $userId)
    {
        try {
            // Prepare SQL query to delete the daily accomplishment record
            $query = "DELETE FROM student_documentation_tbl WHERE file_id = ? AND user_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$fileId, $userId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                // Deletion successful
                return array(
                    "success" => true,
                    "message" => "Record deleted successfully",
                    "file_id" => $fileId,
                    "user_id" => $userId
                );
            } else {
                // No record deleted
                return array(
                    "success" => false,
                    "message" => "No record deleted"
                );
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array(
                "success" => false,
                "message" => "Database error: " . $e->getMessage()
            );
        }
    }

    public function updateTimeIn($data)
    {
        if (!isset($data['dtr_id']) || !isset($data['time_in']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        $dtr_id = $data['dtr_id'];
        $time_in = $data['time_in'];
        $employer_id = $data['employer_id'];
        $student_id = $data['student_id'];

        try {
            // Verify that the employer is linked to the student
            $stmt = $this->db->prepare("SELECT * FROM student_employer_relationship_tbl WHERE employer_id = ? AND student_id = ?");
            $stmt->execute([$employer_id, $student_id]);
            if ($stmt->rowCount() == 0) {
                return array("success" => false, "message" => "Employer is not linked to the student.");
            }

            // Fetch the existing time_out for the provided dtr_id and student_id
            $stmt = $this->db->prepare("SELECT time_out FROM student_dtr_tbl WHERE dtr_id = ? AND user_id = ?");
            $stmt->execute([$dtr_id, $student_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $time_out = $row ? $row['time_out'] : null;

            // Calculate hours worked if time_out is available
            $hours_worked = $time_out ? $this->calculate_hours_worked($time_in, $time_out) : null;

            // Update the time_in and hours_worked in the student_dtr_tbl
            $stmt = $this->db->prepare("UPDATE student_dtr_tbl SET time_in = ?, hours_worked = ? WHERE dtr_id = ? AND user_id = ?");
            $result = $stmt->execute([$time_in, $hours_worked, $dtr_id, $student_id]);

            if ($result) {
                return array("success" => true, "message" => "Time in updated successfully");
            } else {
                return array("success" => false, "message" => "Failed to update time in");
            }
        } catch (PDOException $e) {
            return array("success" => false, "message" => "Database error: " . $e->getMessage());
        }
    }

    public function updateTimeOut($data)
    {
        if (!isset($data['dtr_id']) || !isset($data['time_out']) || !isset($data['employer_id']) || !isset($data['student_id'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        $dtr_id = $data['dtr_id'];
        $time_out = $data['time_out'];
        $employer_id = $data['employer_id'];
        $student_id = $data['student_id'];

        try {
            // Verify that the employer is linked to the student
            $stmt = $this->db->prepare("SELECT * FROM student_employer_relationship_tbl WHERE employer_id = ? AND student_id = ?");
            $stmt->execute([$employer_id, $student_id]);
            if ($stmt->rowCount() == 0) {
                return array("success" => false, "message" => "Employer is not linked to the student.");
            }

            // Fetch the time_in for the provided dtr_id and student_id
            $stmt = $this->db->prepare("SELECT time_in FROM student_dtr_tbl WHERE dtr_id = ? AND user_id = ?");
            $stmt->execute([$dtr_id, $student_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return array("success" => false, "message" => "No record found for the provided dtr_id and student_id.");
            }
            $time_in = $row['time_in'];

            // Calculate hours worked
            $hours_worked = $this->calculate_hours_worked($time_in, $time_out);

            // Update the time_out and hours_worked in the student_dtr_tbl
            $stmt = $this->db->prepare("UPDATE student_dtr_tbl SET time_out = ?, hours_worked = ? WHERE dtr_id = ? AND user_id = ?");
            $result = $stmt->execute([$time_out, $hours_worked, $dtr_id, $student_id]);

            if ($result) {
                return array("success" => true, "message" => "Time out updated successfully");
            } else {
                return array("success" => false, "message" => "Failed to update time out");
            }
        } catch (PDOException $e) {
            return array("success" => false, "message" => "Database error: " . $e->getMessage());
        }
    }

    public function insertExitPoll($data)
    {
        $query = "INSERT INTO student_exitpoll_tbl (
        user_id,
        student_name,
        course_and_year,
        name_of_company,
        assigned_position,
        department,
        job_description,
        supervisor_name,
        ojt_duration,
        total_hours,
        work_related_to_academic_program,
        orientation_on_company_organization,
        given_job_description,
        work_hours_clear,
        felt_safe_and_secure,
        no_difficulty_going_to_and_from_work,
        provided_with_allowance,
        allowance_amount,
        achievement_1_description,
        achievement_1_rating,
        achievement_2_description,
        achievement_2_rating,
        achievement_3_description,
        achievement_3_rating,
        achievement_4_description,
        achievement_4_rating,
        achievement_5_description,
        achievement_5_rating,
        overall_training_experience,
        improvement_suggestion
    ) VALUES (
        :user_id,
        :student_name,
        :course_and_year,
        :name_of_company,
        :assigned_position,
        :department,
        :job_description,
        :supervisor_name,
        :ojt_duration,
        :total_hours,
        :work_related_to_academic_program,
        :orientation_on_company_organization,
        :given_job_description,
        :work_hours_clear,
        :felt_safe_and_secure,
        :no_difficulty_going_to_and_from_work,
        :provided_with_allowance,
        :allowance_amount,
        :achievement_1_description,
        :achievement_1_rating,
        :achievement_2_description,
        :achievement_2_rating,
        :achievement_3_description,
        :achievement_3_rating,
        :achievement_4_description,
        :achievement_4_rating,
        :achievement_5_description,
        :achievement_5_rating,
        :overall_training_experience,
        :improvement_suggestion
    )";

        $stmt = $this->db->prepare($query);

        // Bind values
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':student_name', $data['student_name']);
        $stmt->bindParam(':course_and_year', $data['course_and_year']);
        $stmt->bindParam(':name_of_company', $data['name_of_company']);
        $stmt->bindParam(':assigned_position', $data['assigned_position']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':job_description', $data['job_description']);
        $stmt->bindParam(':supervisor_name', $data['supervisor_name']);
        $stmt->bindParam(':ojt_duration', $data['ojt_duration']);
        $stmt->bindParam(':total_hours', $data['total_hours']);
        $stmt->bindParam(':work_related_to_academic_program', $data['work_related_to_academic_program'], PDO::PARAM_BOOL);
        $stmt->bindParam(':orientation_on_company_organization', $data['orientation_on_company_organization'], PDO::PARAM_BOOL);
        $stmt->bindParam(':given_job_description', $data['given_job_description'], PDO::PARAM_BOOL);
        $stmt->bindParam(':work_hours_clear', $data['work_hours_clear'], PDO::PARAM_BOOL);
        $stmt->bindParam(':felt_safe_and_secure', $data['felt_safe_and_secure'], PDO::PARAM_BOOL);
        $stmt->bindParam(':no_difficulty_going_to_and_from_work', $data['no_difficulty_going_to_and_from_work'], PDO::PARAM_BOOL);
        $stmt->bindParam(':provided_with_allowance', $data['provided_with_allowance'], PDO::PARAM_BOOL);
        $stmt->bindParam(':allowance_amount', $data['allowance_amount']);
        $stmt->bindParam(':achievement_1_description', $data['achievement_1_description']);
        $stmt->bindParam(':achievement_1_rating', $data['achievement_1_rating']);
        $stmt->bindParam(':achievement_2_description', $data['achievement_2_description']);
        $stmt->bindParam(':achievement_2_rating', $data['achievement_2_rating']);
        $stmt->bindParam(':achievement_3_description', $data['achievement_3_description']);
        $stmt->bindParam(':achievement_3_rating', $data['achievement_3_rating']);
        $stmt->bindParam(':achievement_4_description', $data['achievement_4_description']);
        $stmt->bindParam(':achievement_4_rating', $data['achievement_4_rating']);
        $stmt->bindParam(':achievement_5_description', $data['achievement_5_description']);
        $stmt->bindParam(':achievement_5_rating', $data['achievement_5_rating']);
        $stmt->bindParam(':overall_training_experience', $data['overall_training_experience']);
        $stmt->bindParam(':improvement_suggestion', $data['improvement_suggestion']);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function editExitPoll($data)
    {
        // Check if the required fields are present in the data
        if (!isset($data['user_id'])) {
            return array("success" => false, "message" => "Missing required fields");
        }

        $user_id = $data['user_id'];

        // Prepare the SQL query dynamically based on the provided fields
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            if ($key !== 'user_id') {
                $fields[] = "$key = ?";
                $params[] = $value;
            }
        }
        $params[] = $user_id; // Add user_id to the end of the parameters array

        if (empty($fields)) {
            return array("success" => false, "message" => "No fields to update");
        }

        $setClause = implode(", ", $fields);
        $query = "UPDATE student_exitpoll_tbl SET $setClause WHERE user_id = ?";

        try {
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute($params);

            if ($result) {
                return array("success" => true, "message" => "Exit poll updated successfully");
            } else {
                return array("success" => false, "message" => "Failed to update exit poll");
            }
        } catch (PDOException $e) {
            return array("success" => false, "message" => "Database error: " . $e->getMessage());
        }
    }


    private function generate_token($user_id)
    {
        $payload = array(
            "user_id" => $user_id,
            "iat" => time(), // Issued at: current time
            "exp" => time() + (60 * 60) // Expire in 1 hour
        );

        return JWT::encode($payload, $this->secret_key, 'HS256');
    }
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    http_response_code(200);
    exit();
}
