<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $matric_no = $class = "";
$name_err = $matric_no_err = $class_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["idStudents"]) && !empty($_POST["idStudents"])){
    // Get hidden input value
    $idStudents = $_POST["idStudents"];
    
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate address address
    $input_matric_no = trim($_POST["matric_no"]);
    if(empty($input_matric_no)){
        $matric_no_err = "Please enter an address.";     
    } else{
        $matric_no = $input_matric_no;
    }
    
    // Validate salary
    $input_class = trim($_POST["class"]);
    if(empty($input_class)){
        $class_err = "Please enter the salary amount.";     
    } elseif(!ctype_digit($input_class)){
        $class_err = "Please enter a positive integer value.";
    } else{
        $class = $input_class;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($matric_no_err) && empty($class_err)){
        // Prepare an update statement
        $sql = "UPDATE student_info SET name=?, matric_no=?, class=? WHERE idStudents=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_matric_no, $param_class, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_matric_no = $matric_no;
            $param_class = $class;
            $param_id = $idStudents;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["idStudents"]) && !empty(trim($_GET["idStudents"]))){
        // Get URL parameter
        $idStudents =  trim($_GET["idStudents"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM student_info WHERE idStudents = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_idStudents);
            
            // Set parameters
            $param_idStudents = $idStudents;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["name"];
                    $matric_no = $row["matric_no"];
                    $class = $row["class"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the employee record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Matric Number</label>
                            <textarea name="matric_no" class="form-control <?php echo (!empty($matric_no_err)) ? 'is-invalid' : ''; ?>"><?php echo $matric_no; ?></textarea>
                            <span class="invalid-feedback"><?php echo $matric_no_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Class</label>
                            <input type="text" name="class" class="form-control <?php echo (!empty($class_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $class; ?>">
                            <span class="invalid-feedback"><?php echo $class_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $idStudents; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>