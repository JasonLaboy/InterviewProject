<?php

    function ConnectSQL()  
    {  
        $servername = "localhost:4321";
        $username = "WebAccess";
        $password = "1234Qwer";

        // Create connection
        $conn = mysqli_connect($servername, $username, $password);

        // Check connection
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        return $conn;
    } 

    function runSQL($sql){
        try  
        {  
            $conn = ConnectSQL();  
            return $conn->query($sql);
            
        }
        catch(Error $e) {
            $trace = $e->getTrace();
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' called from '.$trace[0]['file'].' on line '.$trace[0]['line'];
        }
    }
    
    if(isset($_REQUEST["ProjectId"])){
        try {  
            $ProjectId = $_REQUEST["ProjectId"];
            
            
            $outPut = "";
            $outPut .= "<table class='mainTable' id='mainTable'>";
            $outPut .= "<tr><th>Task</th><th>Assigned To</th><th>Estimated Hours</th></tr>";
            
            
            $sql = "SELECT distinct  p.Name as ProjectName, Hours, pt.Name as taskName,
                    u.Name as User, p.id as id
                    FROM sys.projecttasks pt 
                    inner join sys.projects p on pt.ProjectId = p.id
                    inner join sys.userprojects up on up.ProjectId = pt.ProjectId
                    inner join sys.users u on pt.UserId = u.id
                    where pt.ProjectId = '$ProjectId'";  
            $result = runSQL($sql);
            $totalHours = 0;
            while($row = $result->fetch_assoc()) {
                $ProjectName = $row['ProjectName'];
                $taskName = $row['taskName'];
                $Hours = $row['Hours'];
                $User = $row['User'];
                $totalHours += $Hours;
                if ($productCount % 2 == 0){
                    $outPut .= "<tr class='oddRow'>";
                }else{
                    $outPut .= "<tr>";
                }
                $outPut .= "<td>$taskName</td><td>$User</td><td>$Hours</td>";
                $outPut .= "</tr>";
                $productCount++;  
            }
            echo "<div><h1 style='float:left;'>$ProjectName</h1><h2 style='float:right; margin-top: 35px;'>$totalHours Hours</h2></div>";
            $outPut .= "</table>";
            $outPut .= "<br><div style='text-align:left; display:none;' id='backButton'><a href='?user=$user'><button>Back</button></a></div>";
            echo $outPut;
        }catch(Error $e) {
            $trace = $e->getTrace();
            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' called from '.$trace[0]['file'].' on line '.$trace[0]['line'];
        }

    }else{
        echo 'error project not found';
    }
    
?>