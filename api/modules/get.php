<?php

class Get
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getEmployerCount()
    {
        $sql = "SELECT COUNT(*) AS count FROM employer_credentials_tbl";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt->execute()) {
            return "Error fetching employer count";
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result ? $result['count'] : 0;
        $stmt->closeCursor();
        return $count;
    }

    public function getInstructorCount()
    {
        $sql = "SELECT COUNT(*) AS count FROM instructor_credentials_tbl";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt->execute()) {
            return "Error fetching instructor count";
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result ? $result['count'] : 0;
        $stmt->closeCursor();
        return $count;
    }

    public function getStudentCount()
    {
        $sql = "SELECT COUNT(*) AS count FROM student_credentials_tbl";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt->execute()) {
            return "Error fetching student count";
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result ? $result['count'] : 0;
        $stmt->closeCursor();
        return $count;
    }

    public function getStudentsForAdmin()
    {
        try {
            $query = "
            SELECT * FROM student_credentials_tbl
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $studentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $studentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching students for admin: " . $e->getMessage());
            return null;
        }
    }

    public function getInstructorsForAdmin()
    {
        try {
            $query = "
            SELECT * FROM instructor_credentials_tbl
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $instructorRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $instructorRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching instructors for admin: " . $e->getMessage());
            return null;
        }
    }

    public function getEmployersForAdmin()
    {
        try {
            $query = "
            SELECT * FROM employer_credentials_tbl
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $employerRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $employerRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching employers for admin: " . $e->getMessage());
            return null;
        }
    }

    public function getLinkedAccountsForAdmin()
    {
        try {
            $query = "
            SELECT sc.*, ec.*
            FROM student_credentials_tbl sc
            JOIN student_employer_relationship_tbl ser ON sc.user_id = ser.student_id
            JOIN employer_credentials_tbl ec ON ser.employer_id = ec.employer_id;
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $linkedRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $linkedRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching linked accounts for admin: " . $e->getMessage());
            return null;
        }
    }

    public function getLinkedAccountsCount()
    {
        $sql = "SELECT COUNT(*) AS count FROM student_employer_relationship_tbl";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt->execute()) {
            return "Error fetching relationship count";
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result ? $result['count'] : 0;
        $stmt->closeCursor();
        return $count;
    }

    public function getStudentProfile($user_id)
    {
        try {
            // Prepare SQL query to fetch student profile based on user_id
            $query = "SELECT * FROM student_credentials_tbl WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch student profile details
                $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                return $profile;
            } else {
                // No student profile found with the given user_id
                return null;
            }
        } catch (PDOException $e) {
            // Handle database errors
            return null;
        }
    }

    public function getStudentName($user_id)
    {
        try {
            // Prepare SQL query to fetch student name based on user_id
            $query = "SELECT student_name FROM student_credentials_tbl WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch student name
                $name = $stmt->fetchColumn();
                return array("student_name" => $name, "message" => "Successfully retrieved data");
            } else {
                // No student name found with the given user_id
                return array("message" => "No student name found with the given user_id");
            }
        } catch (PDOException $e) {
            // Handle database errors
            return array("message" => "Database error: " . $e->getMessage());
        }
    }


    public function getPastTimeRecords($userId)
    {
        try {
            // Prepare SQL query to fetch past time records based on user_id
            $query = "SELECT * FROM student_dtr_tbl WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch past time records
                $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $records;
            } else {
                // No past time records found for the given user_id
                return null;
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error fetching past time records: " . $e->getMessage());
            return null;
        }
    }

    public function getProofOfEvidencesFiles($userId)
    {
        $stmt = $this->conn->prepare("
    SELECT 
        user_id,
        'CCS Events' AS category,
        file_name
    FROM 
        student_ccs_picture_tbl
    WHERE 
        user_id = ?
    UNION ALL
    SELECT 
        user_id,
        'Documentation (OJT Work Photos)' AS category,
        file_name
    FROM 
        student_documentation_tbl
    WHERE 
        user_id = ?
    UNION ALL
    SELECT 
        user_id,
        'Seminar Certificates' AS category,
        file_name
    FROM 
        student_seminar_certificate_tbl
    WHERE 
        user_id = ?
    UNION ALL
    SELECT 
        user_id,
        'GC Events' AS category,
        file_name
    FROM 
        student_sportsfest_picture_tbl
    WHERE 
        user_id = ?
");

        $stmt->execute([$userId, $userId, $userId]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getRecordedDailyAccomplishments($userId)
    {
        try {
            // Prepare SQL query to fetch recorded daily accomplishments based on user_id
            $query = "SELECT * FROM student_daily_accomplishments_tbl WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$userId]);

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch recorded daily accomplishments
                $accomplishments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $accomplishments;
            } else {
                // No recorded daily accomplishments found for the given user_id
                return null;
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error fetching recorded daily accomplishments: " . $e->getMessage());
            return null;
        }
    }

    public function getEducationRecords($user_id)
    {
        try {
            // Prepare SQL query to fetch education records
            $query = "SELECT * FROM student_portfolio_education_tbl WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);

            // Fetch education records
            $educationRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if any records were found
            if ($educationRecords) {
                // Records found, return them
                return array(
                    "success" => true,
                    "message" => "Education records fetched successfully",
                    "education_records" => $educationRecords
                );
            } else {
                // No records found
                return array(
                    "success" => false,
                    "message" => "No education records found for the user"
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

    public function getSkillsRecords($user_id)
    {
        try {
            // Prepare SQL query to fetch skills records
            $query = "SELECT * FROM student_portfolio_skills_tbl WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);

            // Fetch skills records
            $skillsRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if any records were found
            if ($skillsRecords) {
                // Records found, return them
                return array(
                    "success" => true,
                    "message" => "Skills records fetched successfully",
                    "skills_records" => $skillsRecords
                );
            } else {
                // No records found
                return array(
                    "success" => false,
                    "message" => "No skills records found for the user"
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

    public function getUserPhoto($user_id)
    {
        // Fetch the profile photo path from the database
        $stmt = $this->conn->prepare("SELECT image_path FROM student_profile_picture_tbl WHERE user_id = ?");
        $stmt->execute([$user_id]); // Bind parameters directly in the execute method
        $image_path = $stmt->fetchColumn(); // Fetch the image path directly

        // Log SQL query and image path
        error_log("SQL Query: " . $stmt->queryString);
        error_log("Image Path: " . $image_path);

        // If a photo exists for the user, return the photo path
        if (!empty($image_path)) {
            // Check if the image_path starts with '../'
            if (strpos($image_path, '../') === 0) {
                // Adjust the file path to include the 'api' folder
                // Assuming the 'api' folder is one level above, remove "../" prefix
                $image_path = ltrim($image_path, '../');
            }
            return array("success" => true, "image_path" => $image_path);
        } else {
            // If no photo found, return error message
            return array("success" => false, "message" => "No profile photo found for the user.");
        }
    }

    public function getAccomplishmentsForEmployer($employer_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_daily_accomplishments_tbl a
            JOIN student_credentials_tbl s ON a.user_id = s.user_id
            JOIN student_employer_relationship_tbl r ON s.user_id = r.student_id
            WHERE r.employer_id = :employer_id
            AND s.user_id = :student_id;
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':employer_id', $employer_id, PDO::PARAM_INT);

            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $accomplishmentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $accomplishmentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching accomplishment records for instructor and student: " . $e->getMessage());
            return null;
        }
    }

    public function getEmployerFeedback($student_id)
    {
        try {
            // Prepare SQL query to fetch employer feedback based on student_id
            $query = "SELECT * FROM employer_feedback_tbl WHERE student_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$student_id]);

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch employer feedback records
                $feedbackRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $feedbackRecords;
            } else {
                // No employer feedback found for the given student_id
                return null;
            }
        } catch (PDOException $e) {
            // Handle database errors
            return null;
        }
    }

    public function getAssociatedStudents($employer_id)
    {
        try {
            // Prepare SQL query to fetch associated students based on employer_id
            $query = "
            SELECT stu.*
            FROM student_credentials_tbl stu
            INNER JOIN student_employer_relationship_tbl rel ON stu.user_id = rel.student_id
            WHERE rel.employer_id = ?
        ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$employer_id]);

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch associated students
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $students;
            } else {
                // No associated students found for the given employer_id
                return null;
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error fetching associated students for employer: " . $e->getMessage());
            return null;
        }
    }

    public function getUploadedCertificates($employerId, $studentId)
    {
        // Query to fetch uploaded certificates for the given employerId and studentId
        $stmt = $this->conn->prepare("SELECT * FROM certificate_of_completion_tbl WHERE employer_id = ? AND student_id = ?");
        $stmt->execute([$employerId, $studentId]);
        $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($certificates) {
            return array("success" => true, "certificates" => $certificates);
        } else {
            return array("success" => false, "message" => "No certificates found for the specified employer and student.");
        }
    }    

    public function getEducationRecordsForEmployer($employer_id)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT spe.*
                FROM student_portfolio_education_tbl spe
                JOIN student_credentials_tbl sc ON spe.user_id = sc.user_id
                JOIN student_employer_relationship_tbl ser ON sc.user_id = ser.student_id
                WHERE ser.employer_id = ?");
            $stmt->execute([$employer_id]);
            $educationRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if there are no records fetched
            if (empty($educationRecords)) {
                return array("message" => "No education records found for the employer");
            }

            return $educationRecords;
        } catch (PDOException $e) {
            // Log the error or handle it appropriately
            error_log("Error fetching education records: " . $e->getMessage());
            return null; // Return null to indicate failure
        }
    }

    public function getSkillsRecordsForEmployer($employer_id)
    {
        try {
            $stmt = $this->conn->prepare("
                SELECT ske.*
                FROM student_portfolio_skills_tbl ske
                JOIN student_credentials_tbl sc ON ske.user_id = sc.user_id
                JOIN student_employer_relationship_tbl ser ON sc.user_id = ser.student_id
                WHERE ser.employer_id = ?");
            $stmt->execute([$employer_id]);
            $skillsRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check if there are no records fetched
            if (empty($skillsRecords)) {
                return array("message" => "No skill records found for the employer");
            }

            return $skillsRecords;
        } catch (PDOException $e) {
            // Log the error or handle it appropriately
            error_log("Error fetching skill records: " . $e->getMessage());
            return null; // Return null to indicate failure
        }
    }

    public function getStudentProfilePicture($employerId, $studentId)
    {
        $query = "SELECT spp.image_path 
              FROM student_profile_picture_tbl spp 
              JOIN student_employer_relationship_tbl ser ON spp.user_id = ser.student_id 
              WHERE ser.employer_id = ? AND ser.student_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$employerId, $studentId]);

        $imagePath = $stmt->fetchColumn(); // Fetch the image path directly

        // Log SQL query and image path
        error_log("SQL Query: " . $stmt->queryString);
        error_log("Image Path: " . $imagePath);

        // If a photo exists for the user, return the photo path
        if (!empty($imagePath)) {
            // Check if the image_path starts with '../'
            if (strpos($imagePath, '../') === 0) {
                // Adjust the file path to include the 'api' folder
                // Assuming the 'api' folder is one level above, remove "../" prefix
                $imagePath = ltrim($imagePath, '../');
            }
            return array("success" => true, "image_path" => $imagePath);
        } else {
            // If no photo found, return error message
            return array("success" => false, "message" => "No profile photo found for the user.");
        }
    }

    public function getCertificateOfCompletionForStudent($student_id)
    {
        try {
            $stmt = $this->conn->prepare("SELECT c.file_name, c.file_path, e.employer_name, e.employer_email, e.company_name, e.employer_position 
                                      FROM certificate_of_completion_tbl c
                                      INNER JOIN employer_credentials_tbl e ON c.employer_id = e.employer_id
                                      WHERE c.student_id = ?");
            $stmt->execute([$student_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                return array("success" => false, "message" => "Certificate of completion not found for the student.");
            }

            // Return the certificate information along with employer details
            return array("success" => true, "certificate_info" => $result);
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Error retrieving certificate of completion for student ID: " . $student_id . " Error: " . $e->getMessage());
            return array("success" => false, "message" => "An error occurred while retrieving the certificate of completion.");
        }
    }

    public function getAssociatedStudentsForInstructor($instructor_id)
    {
        try {
            $query = "
            SELECT stu.*, emp.*
            FROM student_credentials_tbl stu
            INNER JOIN student_instructor_relationship_tbl rel ON stu.user_id = rel.student_id
            LEFT JOIN student_employer_relationship_tbl emp_rel ON stu.user_id = emp_rel.student_id
            LEFT JOIN employer_credentials_tbl emp ON emp_rel.employer_id = emp.employer_id
            WHERE rel.instructor_id = :instructor_id;
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                // Fetch associated students
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $students;
            } else {
                // No associated students found for the given employer_id
                return null;
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error fetching associated students for instructor: " . $e->getMessage());
            return null;
        }
    }

    public function getInstructorName($instructor_id)
    {
        try {
            $query = "SELECT instructor_name FROM instructor_credentials_tbl WHERE instructor_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$instructor_id]);

            if ($stmt->rowCount() > 0) {
                $instructorName = $stmt->fetchColumn();
                return array("instructor_name" => $instructorName, "message" => "Successfully retrieved data");
            } else {
                return array("message" => "No instructor name found with the given instructor_id");
            }
        } catch (PDOException $e) {
            return array("message" => "Database error: " . $e->getMessage());
        }
    }

    public function getEmployerName($employer_id)
    {
        try {
            $query = "SELECT employer_name FROM employer_credentials_tbl WHERE employer_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$employer_id]);

            if ($stmt->rowCount() > 0) {
                $employerName = $stmt->fetchColumn();
                return array("employer_name" => $employerName, "message" => "Successfully retrieved data");
            } else {
                return array("message" => "No instructor name found with the given employer_id");
            }
        } catch (PDOException $e) {
            return array("message" => "Database error: " . $e->getMessage());
        }
    }

    public function getStudentDTRForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT d.*, 
                   TIMESTAMPDIFF(SECOND, d.time_in, d.time_out) / 3600 AS hours_worked
            FROM student_dtr_tbl d
            JOIN student_credentials_tbl s ON d.user_id = s.user_id
            JOIN student_instructor_relationship_tbl r ON s.user_id = r.student_id
            WHERE r.instructor_id = :instructor_id
            AND s.user_id = :student_id;
        ";
            $stmt = $this->conn->prepare($query);

            // Bind the instructor_id parameter
            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            // Bind the student_id parameter
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            // Execute the query
            $stmt->execute();

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch time_in, time_out, and student_name records
                $dtrRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $dtrRecords;
            } else {
                // No time_in, time_out, and student_name records found for the given instructor_id and student_id
                return null;
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error fetching time_in, time_out, and student_name records for instructor and student: " . $e->getMessage());
            return null;
        }
    }

    public function getTimeInOutForEmployer($employer_id, $student_id)
    {
        try {
            // Prepare SQL query to fetch time_in, time_out, and student_name records based on employer_id
            $query = "
            SELECT d.*, 
                   TIMESTAMPDIFF(SECOND, d.time_in, d.time_out) / 3600 AS hours_worked
            FROM student_dtr_tbl d
            JOIN student_credentials_tbl s ON d.user_id = s.user_id
            JOIN student_employer_relationship_tbl r ON s.user_id = r.student_id
            WHERE r.employer_id = :employer_id
            AND s.user_id = :student_id;

        ";
            $stmt = $this->conn->prepare($query);

            // Bind the instructor_id parameter
            $stmt->bindParam(':employer_id', $employer_id, PDO::PARAM_INT);
            // Bind the student_id parameter
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

             // Execute the query
             $stmt->execute();

            // Check if any rows were returned
            if ($stmt->rowCount() > 0) {
                // Fetch time_in, time_out, and student_name records
                $timeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $timeRecords;
            } else {
                // No time_in, time_out, and student_name records found for the given employer_id
                return null;
            }
        } catch (PDOException $e) {
            // Log the error
            error_log("Error fetching time_in, time_out, and student_name records for employer: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentAccomplishmentsForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_daily_accomplishments_tbl a
            JOIN student_credentials_tbl s ON a.user_id = s.user_id
            JOIN student_instructor_relationship_tbl r ON s.user_id = r.student_id
            WHERE r.instructor_id = :instructor_id
            AND s.user_id = :student_id;
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);

            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $accomplishmentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $accomplishmentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching accomplishment records for instructor and student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentCertificatesForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT cc.file_name, cc.file_path,
            ec.employer_name, ec.employer_email, ec.company_name, ec.employer_position
            FROM certificate_of_completion_tbl cc
            JOIN student_credentials_tbl sc ON cc.student_id = sc.user_id
            JOIN student_instructor_relationship_tbl sir ON sc.user_id = sir.student_id
            JOIN employer_credentials_tbl ec ON cc.employer_id = ec.employer_id
            WHERE sir.instructor_id = :instructor_id
            AND cc.student_id = :student_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);

            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $certificateRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $certificateRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching certificates for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentApplicationLetter($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_signed_application_letter_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentAcceptanceLetter($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_signed_acceptance_letter_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentMOA($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_signed_moa_letter_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentVaccinationCard($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_vaccination_card_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentBarangayClearance($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_barangay_clearance_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentMedicalCertificate($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_medical_certificate_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentResume($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_resume_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentEndorsementLetter($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_signed_endorsement_letter_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentParentsConsent($instructor_id, $student_id)
    {
        try {
            $query = "
        SELECT a.*
        FROM student_signed_parents_consent_letter_tbl a
        JOIN student_instructor_relationship_tbl sirt
        ON a.user_id = sirt.student_id
        WHERE sirt.instructor_id = :instructor_id
        AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentProfilePictureForInstructor($instructorId, $studentId)
    {
        $query = "SELECT spp.image_path 
              FROM student_profile_picture_tbl spp 
              JOIN student_instructor_relationship_tbl ser ON spp.user_id = ser.student_id 
              WHERE ser.instructor_id = ? AND ser.student_id = ?
              ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$instructorId, $studentId]);

        $imagePath = $stmt->fetchColumn(); // Fetch the image path directly

        // Log SQL query and image path
        error_log("SQL Query: " . $stmt->queryString);
        error_log("Image Path: " . $imagePath);

        // If a photo exists for the user, return the photo path
        if (!empty($imagePath)) {
            // Check if the image_path starts with '../'
            if (strpos($imagePath, '../') === 0) {
                // Adjust the file path to include the 'api' folder
                // Assuming the 'api' folder is one level above, remove "../" prefix
                $imagePath = ltrim($imagePath, '../');
            }
            return array("success" => true, "image_path" => $imagePath);
        } else {
            // If no photo found, return error message
            return array("success" => false, "message" => "No profile photo found for the user.");
        }
    }

    public function getEmployerFeedbackForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT fb.*
            FROM employer_feedback_tbl fb
            JOIN student_credentials_tbl sc ON fb.student_id = sc.user_id
            JOIN student_instructor_relationship_tbl sir ON sc.user_id = sir.student_id
            WHERE sir.instructor_id = :instructor_id
            AND fb.student_id = :student_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);

            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $feedbackRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $feedbackRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching certificates for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentCCSPOE($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_ccs_picture_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentAcquaintancePOE($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_acquaintance_picture_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentSeminarPOE($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_seminar_certificate_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentFoundationWeekPOE($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_foundation_week_picture_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getStudentGCEventsPOE($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_sportsfest_picture_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getRequirementStatusForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT * FROM instructor_requirement_checking_tbl WHERE instructor_id = :instructor_id AND student_id = :student_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);

            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $statusRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $statusRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching accomplishment records for instructor and student: " . $e->getMessage());
            return null;
        }
    }

    public function getAnnouncements($instructor_id)
    {
        try {
            $query = "
            SELECT * FROM instructor_announcement_tbl WHERE instructor_id = :instructor_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $announcementRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $announcementRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching announcements for instructor: " . $e->getMessage());
            return null;
        }
    }

    public function getAnnouncementsForStudent($user_id)
    {
        try {
            $query = "
            SELECT ia.announcement_id, ia.title, ia.body, ia.announcement_timestamp
            FROM instructor_announcement_tbl ia
            JOIN student_instructor_relationship_tbl sir ON ia.instructor_id = sir.instructor_id
            WHERE sir.student_id = :student_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':student_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $announcementRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $announcementRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching accomplishment records for instructor and student: " . $e->getMessage());
            return null;
        }
    }

    public function getRequirementStatusForStudent($user_id)
    {
        try {
            $query = "
            SELECT * FROM instructor_requirement_checking_tbl WHERE student_id = :student_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':student_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $statusRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $statusRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching accomplishment records for instructor and student: " . $e->getMessage());
            return null;
        }
    }

    public function getDTRForStudent($user_id)
    {
        try {
            $query = "
        SELECT *
        FROM student_file_dtr_tbl
        WHERE user_id = :user_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getWeeklyAccomplishmentsForStudent($user_id)
    {
        try {
            $query = "
        SELECT *
        FROM student_weekly_accomplishments_tbl
        WHERE user_id = :user_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getDocumentationForStudent($user_id)
    {
        try {
            $query = "
        SELECT *
        FROM student_documentation_tbl
        WHERE user_id = :user_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getFinalReportForStudent($user_id)
    {
        try {
            $query = "
        SELECT *
        FROM student_final_report_tbl
        WHERE user_id = :user_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $finalReportRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $finalReportRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getDTRForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_file_dtr_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $dtrRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $dtrRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getFinalReportForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_final_report_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $finalReportRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $finalReportRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getWeeklyAccomplishmentsForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_weekly_accomplishments_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $weeklyAccomplishmentsRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $weeklyAccomplishmentsRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getDocumentationForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_documentation_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $documentationRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $documentationRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getWeeklyAccomplishmentsForEmployer($employer_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_weekly_accomplishments_tbl a
            JOIN student_employer_relationship_tbl ser
            ON a.user_id = ser.student_id
            WHERE ser.employer_id = :employer_id
            AND ser.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':employer_id', $employer_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $weeklyAccomplishmentsRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $weeklyAccomplishmentsRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getDTRForEmployer($employer_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_file_dtr_tbl a
            JOIN student_employer_relationship_tbl ser
            ON a.user_id = ser.student_id
            WHERE ser.employer_id = :employer_id
            AND ser.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':employer_id', $employer_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $dtrRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $dtrRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getExitPoll($user_id)
    {
        try {
            $query = "
            SELECT * FROM student_exitpoll_tbl WHERE user_id = :user_id
            ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $exitPollRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $exitPollRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching exit poll for student: " . $e->getMessage());
            return null;
        }
    }

    public function getExitPollForInstructor($instructor_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_exitpoll_tbl a
            JOIN student_instructor_relationship_tbl sirt
            ON a.user_id = sirt.student_id
            WHERE sirt.instructor_id = :instructor_id
            AND sirt.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $exitPollRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $exitPollRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getExitPollForEmployer($employer_id, $student_id)
    {
        try {
            $query = "
            SELECT a.*
            FROM student_exitpoll_tbl a
            JOIN student_employer_relationship_tbl ser
            ON a.user_id = ser.student_id
            WHERE ser.employer_id = :employer_id
            AND ser.student_id = :student_id
        ";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':employer_id', $employer_id, PDO::PARAM_INT);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $exitPollRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $exitPollRecords;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            error_log("Error fetching documents for of student: " . $e->getMessage());
            return null;
        }
    }

    public function getAllRequirements($instructor_id, $student_id)
{
    try {
        $result = [];
        
        // Query for instructor_requirement_checking_tbl
        $query1 = "SELECT * FROM instructor_requirement_checking_tbl WHERE instructor_id = :instructor_id AND student_id = :student_id";
        $stmt1 = $this->conn->prepare($query1);
        $stmt1->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt1->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt1->execute();
        if ($stmt1->rowCount() > 0) {
            $result['instructor_requirements'] = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        }

        // Query for student_dtr_file_tbl
        $query2 = "SELECT * FROM student_file_dtr_tbl WHERE user_id = :student_id";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt2->execute();
        if ($stmt2->rowCount() > 0) {
            $result['student_dtr_files'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        // Query for student_exitpoll_tbl
        $query3 = "SELECT * FROM student_exitpoll_tbl WHERE user_id = :student_id";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt3->execute();
        if ($stmt3->rowCount() > 0) {
            $result['student_exitpolls'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        }

        // Query for student_final_report_tbl
        $query4 = "SELECT * FROM student_final_report_tbl WHERE user_id = :student_id";
        $stmt4 = $this->conn->prepare($query4);
        $stmt4->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt4->execute();
        if ($stmt4->rowCount() > 0) {
            $result['student_final_reports'] = $stmt4->fetchAll(PDO::FETCH_ASSOC);
        }

        // Query for student_weekly_accomplishments_tbl
        $query5 = "SELECT * FROM student_weekly_accomplishments_tbl WHERE user_id = :student_id";
        $stmt5 = $this->conn->prepare($query5);
        $stmt5->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt5->execute();
        if ($stmt5->rowCount() > 0) {
            $result['student_weekly_accomplishments'] = $stmt5->fetchAll(PDO::FETCH_ASSOC);
        }

        // Query for certificate_of_completion_tbl
        $query6 = "SELECT * FROM certificate_of_completion_tbl WHERE student_id = :student_id";
        $stmt6 = $this->conn->prepare($query6);
        $stmt6->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt6->execute();
        if ($stmt6->rowCount() > 0) {
            $result['certificates_of_completion'] = $stmt6->fetchAll(PDO::FETCH_ASSOC);
        }

        return !empty($result) ? $result : null;
        
    } catch (PDOException $e) {
        error_log("Error fetching all requirements for student: " . $e->getMessage());
        return null;
    }
}

}
