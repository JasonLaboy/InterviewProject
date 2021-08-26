<html>
    <head>
        <style>
            .mainTable td{
                padding:10px;
                width:25%;
                background:#EFEFEF;
                /*border: solid 0px black;
                border-left: solid 1px black;
                border-right: solid 1px black;*/
            }
            .mainTable th{
                padding:10px;
                width:25%;
                background:#AFAFAF;
                /*border: solid 0px black;
                border-left: solid 1px black;
                border-right: solid 1px black;
                border-top: solid 1px black;*/
            }
            
            .mainTable tr{
                border: solid 0px black;
            }

            .oddRow td{
                background:#DFDFDF;
            }
            
            .mainTable{
                width:100%;
                text-align:left;
                border: 1px black;
            }
            .homeButtons{
                text-decoration: none; 
                font-size:20px;
                padding:10px;
                margin:auto;
                width:20%;
                display:block;
                background:#d1dcdc;
                color:black;
            }
            .homeButtons:hover{
                background:#EAEAEA;

            }
        </style>
        <title>To-do List</title>
    </head>
    <body>

            
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

            
           
        ?>
        <div style="text-align:center; margin:auto;"> 
            <div style="text-align:center; margin:auto; width:80%;">
                <?php 

                    
                    //$conn = ConnectSQL();  
                    if(isset($_GET['user'])){
                        try {  
                            $user = $_GET['user'];
                            $result = runSQL("SELECT * FROM sys.users WHERE Name = '$user'");
                            while($row = $result->fetch_assoc()) {
                                    $userId = $row["id"];
                                    $user = $row["Name"];
                                    $productCount++;  
                            }
                            echo "<div id='mainDiv'>";
                            echo "<div id='header'>";
                            echo "<h1 style='text-align:left;'>";
                            echo $user;
                            echo "'s Project list</h1>";
                            echo "</div>";

                            echo "<table class='mainTable' id='mainTable'>";
                            echo "<tr><th>Project</th><th>Members</th><th>Estimated Hours</th><th>Actions</th></tr>";
                            
                            
                            $sql = "SELECT p.Name as ProjectName, sum(Hours) as Hours, 
                                    GROUP_CONCAT( DISTINCT u.Name SEPARATOR', ' ) AS Members, p.id as id
                                    FROM sys.projects p 
                                    inner join sys.userprojects up on up.ProjectId = p.id 
                                    inner join sys.projecttasks pt on pt.ProjectId = up.ProjectId
                                    inner join sys.users u on pt.UserId = u.id
                                    where up.UserId = '$userId'
                                    group by up.ProjectId";  
                            $result = runSQL($sql);
                            while($row = $result->fetch_assoc()) {
                                $projectName = $row['ProjectName'];
                                $Hours = $row['Hours'];
                                $Members = $row['Members'];
                                $ProjectId = $row['id'];
                                if ($productCount % 2 == 0){
                                    echo "<tr class='oddRow'>";
                                }else{
                                    echo "<tr>";
                                }
                                    echo "<td>$projectName</td><td>$Members</td><td>$Hours</td><td><input type='button' value='View' OnClick='getProject($ProjectId);' /></td>";
                                echo "</tr>";
                                $productCount++;  
                            }
                            echo "</table>";
                            echo "</div>";
                            echo "<br><div style='text-align:left; display:none;' id='backButton'><a href='?user=$user'><button>Back</button></a></div>";
                        }catch(Error $e) {
                            $trace = $e->getTrace();
                            echo $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' called from '.$trace[0]['file'].' on line '.$trace[0]['line'];
                        }
                    }else{
                        $result = runSQL("SELECT * FROM sys.users");
                        while($row = $result->fetch_assoc()) {
                                $user = $row["Name"];
                                echo "<a class='homeButtons' href='?user=$user'>$user</a><br>";
                                $productCount++;  
                        }
                    }
                    

                ?>
            </div> 





        </div>

        <script>
            function getProject(ProjectId){
                document.getElementById('backButton').style.display = 'block';

                var xhttp = new XMLHttpRequest();
                xhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("mainDiv").innerHTML = this.responseText;
                        document.getElementById("header").innerHTML = "";
                    }
                };
                xhttp.open("GET", "actions.php?ProjectId=" + ProjectId, true);
                xhttp.send();
            }
        </script>
    </body>
</html>

