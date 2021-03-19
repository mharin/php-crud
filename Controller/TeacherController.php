<?php

use JetBrains\PhpStorm\NoReturn;

class TeacherController
{
    private PDO $pdo;

    public function __construct()
    {
        $connection = new DbConnect();
        $this->pdo = $connection->connect();
    }
    public function showTeacherInfo($teacherName) : void
    {
        $teacher = teacherLoader::getTeacher($teacherName, $this->pdo);
        require 'View/teacherView.php';
    }

    public function getTeacherInfo(int $id): teacher
    {
        $teacher = teacherLoader::getTeacher($id, $this->pdo);
        require 'View/teacherEdit.php';
    }

    public function getAllTeachersInfo(): void
    {
        $allTeachers = teacherLoader::getAllAssignedTeachers($this->pdo);
        require 'View/teachers.php';
    }

    public function goToCreateTeacher($GET): void
    {
        $groups = groupLoader::getAllUnasignedGroups($this->pdo);
        require 'View/teacherCreate.php';
    }


    #[NoReturn] public function createNewTeacher($GET): void
    {
        $group = groupLoader::getGroup($GET['className'], $this->pdo);
        $teacher = new teacher($GET['lastName'],$GET['firstName'], $GET['email'], $group, teacherLoader::getAllStudentsOfGroup($GET['className'],$this->pdo));

        teacherLoader::saveTeacher($teacher, $this->pdo);

        header("location: ?page=teachers");
        exit;

    }
//should the edit also conclude delete->Teacher?
    public function editTeacher($id): void
    {
        $teacher = teacherLoader::getTeacher($_GET['id']);
        $teacher->setLastName($_POST['lastName']);
        $teacher->setFirstName($_POST['firstName']);
        $teacher->setEmail($_POST['email']);
       // $teacher->setStudents($_POST['students']);
        $teacher->setGroup(new group($_POST['className']));

        teacherLoader::saveTeacher($teacher, $this->pdo);
        require 'View/teacherEdit.php';
    }


//-------------  checks for correct work of buttons DO NOT REMOVE NOR CHANGE!!!-----------------

//check if data were SAVE
    public function saveData(): void
    {
        teacherLoader::saveTeacher(new teacher($_POST['lastName'],$_POST['firstName'], $_POST['email'], new group($_POST['className']), teacherLoader::getAllStudentsOfGroup($_POST['className'],$this->pdo),$_POST['id']),$this->pdo);
    }

//checking if there is an edit parameter ->edit page
    public function checkEditTeacher(): void
    {
        if (isset($_GET['edit'])) {
            $teacher = teacherLoader::getTeacher($_GET['edit'], $this->pdo);
            $allGroups=groupLoader::getAllAssignedGroups($this->pdo);
            require 'View/teacherEdit.php';
        }
    }
    public function checkRemoveTeacher(): void
    {
        if (isset($_POST['delete'])) {
            teacherLoader::deleteTeacher($_POST['id'], $this->pdo);
            //require 'View/studentView.php';
        }
    }
}

