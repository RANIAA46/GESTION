<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gestionabsence");

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

if (isset($_POST['role']) && isset($_POST['email']) && isset($_POST['password'])) {
    $role = $_POST['role'];
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    if ($role === 'etudiant') {
        $sql = "SELECT * FROM etudiant WHERE Email_Etud = '$email' AND Date_De_Naiss = '$password'";
    } elseif ($role === 'enseignant') {
        $sql = "SELECT * FROM enseignant WHERE Email_ens = '$email' AND Date_Naiss_ENS = '$password'";
    } elseif ($role === 'chef') {
        $sql = "SELECT * FROM departement WHERE Email_Dep = '$email' AND Date_Naiss_Dep = '$password'";
    } else {
        echo "<script>alert('نوع المستخدم غير صالح'); window.history.back();</script>";
        exit();
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($role === 'etudiant') {
            $_SESSION['id_etud'] = $user['Id_Etud'];
            $_SESSION['matricule'] = $user['matricule']; 
            echo "<script>
                alert('تم تسجيل الدخول كطالب');
                window.location.href = 'etudiantsusecase.html';  
            </script>";
        } elseif ($role === 'enseignant') {
            $_SESSION['id_ens'] = $user['Id_Ens'];
            echo "<script>
                alert('تم تسجيل الدخول كأستاذ');
                window.location.href = 'dashboard_enseignant.php';
            </script>";
        } elseif ($role === 'chef') {
            $_SESSION['id_chef'] = $user['Id_Dep'];
            echo "<script>
                alert('تم تسجيل الدخول كرئيس قسم');
                window.location.href = 'chefDepartement.html';
            </script>";
        }
    } else {
        echo "<script>
            alert('بيانات غير صحيحة، حاول مجددًا');
            window.history.back();
        </script>";
    }
} else {
    echo "<script>alert('يرجى ملء جميع الحقول'); window.history.back();</script>";
}

$conn->close();
?>
