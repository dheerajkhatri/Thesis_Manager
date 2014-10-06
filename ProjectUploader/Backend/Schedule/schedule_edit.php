<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <?php include '../../config/config.php'; ?>
        <link rel="stylesheet" href="<?php echo constant("HOST11") . '/web/css/AdminStyleSheet.css' ?>" type="text/css" />
        <link rel="stylesheet" href="../../web/css/adminStudentStyleSheet.css" type="text/css" />
        <title>Thesis Manager (Admin Section)</title>
        <?php
        //Forward to login page if not authenticated.
        session_start();
        if (!isset($_SESSION['admin_user_nm'])) {
            header("Location: " . constant("HOST11") . "/Backend/login.php");
        }
        
        if (isset($_GET['class'])) {
            $roll = $_GET['class'];
            if ($roll != "MT" && $roll != "BT") {
                $roll = "NONE";
            }
        } else {
            $roll = "NONE";
        }
        if ($roll == "NONE") {
            $result = "NONE";
        } else if (!($con = mysql_connect(constant("HOSTNAME"), constant("USERNAME"), constant("PASS")))) {
            $result = "DBCONNECTION_ERROR";
        } else if (!($select = mysql_select_db(constant("DBNAME"), $con))) {
            $result = "DBCONNECTION_ERROR";
        } else {
            $sql = "SELECT last_date FROM schedule where class='" . $roll . "'";
            $rs = mysql_query($sql);
            $row = mysql_fetch_assoc($rs);
            if ($row) {
                $dateArray = explode("-", $row['last_date']);
                $last_date = $dateArray[2] . '/' . $dateArray[1] . '/' . $dateArray[0];
                $result = "DONE";
            }
            mysql_close($con);
        }
        ?>

        <script type="text/javascript" language="javascript">
            var mth = new Array(' ','january','february','march','april','may','june','july','august','september','october','november','december');
            var day = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
            function stripBlanks(fld) {
                var result = "";
                var c=0;
                for (i=0; i<fld.length; i++) {
                    if (fld.charAt(i) != " " || c > 0) {
                        result += fld.charAt(i);
                        if (fld.charAt(i) != " ") c = result.length;
                    }
                }
                return result.substr(0,c);
            }

            var numb = '0123456789';
            function isValid(parm,val) {
                if (parm == "") return true;
                for (i=0; i<parm.length; i++) {
                    if (val.indexOf(parm.charAt(i),0) == -1)
                        return false;
                }
                return true;
            }

            function isNumber(parm) {
                return isValid(parm,numb);
            }
            
            function validateDate(fld,fmt,rng) {
                //fld ==> is the date in dd/mm/yyyy.
                //fmt ==> is format must be 'W' for dd/mm/yyyy
                //rng==> is range and must be 'A' for any date.
                var dd, mm, yy;
                var today = new Date;
                var t = new Date;
                fld = stripBlanks(fld);
                if (fld == '') return false;
                var d1 = fld.split('/');
                if (d1.length != 3) d1 = fld.split(' ');
                if (d1.length != 3) return false;
                if (fmt == 'u' || fmt == 'U') {
                    dd = d1[1];
                    mm = d1[0];
                    yy = d1[2];
                }
                else if (fmt == 'j' || fmt == 'J') {
                    dd = d1[2];
                    mm = d1[1];
                    yy = d1[0];
                }
                else if (fmt == 'w' || fmt == 'W'){
                    dd = d1[0];
                    mm = d1[1];
                    yy = d1[2];
                }
                else return false;
                var n = dd.lastIndexOf('st');
                if (n > -1) dd = dd.substr(0,n);
                n = dd.lastIndexOf('nd');
                if (n > -1) dd = dd.substr(0,n);
                n = dd.lastIndexOf('rd');
                if (n > -1) dd = dd.substr(0,n);
                n = dd.lastIndexOf('th');
                if (n > -1) dd = dd.substr(0,n);
                n = dd.lastIndexOf(',');
                if (n > -1) dd = dd.substr(0,n);
                n = mm.lastIndexOf(',');
                if (n > -1) mm = mm.substr(0,n);
                if (!isNumber(dd)) return false;
                if (!isNumber(yy)) return false;
                if (!isNumber(mm)) {
                    var nn = mm.toLowerCase();
                    for (var i=1; i < 13; i++) {
                        if (nn == mth[i] ||
                            nn == mth[i].substr(0,3)) {
                            mm = i;
                            i = 13;
                        }
                    }
                }
                if (!isNumber(mm)) return false;
                dd = parseFloat(dd);
                mm = parseFloat(mm);
                yy = parseFloat(yy);
                if (yy < 100) yy += 2000;
                if (yy < 1582 || yy > 4881) return false;
                if (mm == 2 && (yy%400 == 0 || (yy%4 == 0 && yy%100 != 0))) day[1]=29;else day[1]=28;
                if (mm < 1 || mm > 12) return false;
                if (dd < 1 || dd > day[mm-1]) return false;
                t.setDate(dd);
                t.setMonth(mm-1);
                t.setFullYear(yy);
                if (rng == 'p' || rng == 'P') {
                    if (t > today) return false;
                }
                else if (rng == 'f' || rng == 'F') {
                    if (t < today) return false;
                }else if (rng != 'a' && rng != 'A') return false;
                return true;
            }
            function submitForEdition(){
                var dt=document.getElementById('txtDate');
                if(validateDate(dt.value, 'w','A')){
                    document.forms["editSchedule"].submit();
                }else{
                    alert("Not a valid date. Enter a valid date in correct format.");
                }
            }
        </script>
    </head>
    <body>
        <div id="bodyPanel">
            <div id="adminHeader">
                <div id="adminHeaderTitle"><?php include '../PhpIncludeFiles/Admin/headerImage.php';?></div>
                <div id="adminHeaderLocation">Schedule Management</div>
            </div>
            <div id="adminVMenu">
                <?php include '../PhpIncludeFiles/Admin/VerticalMenuItems.php'; ?>
            </div>
            <div id="adminMiddle">
                <?php include '../PhpIncludeFiles/Admin/schedule/ScheduleHorizontalMenu.php'; ?>
                <div id="adminMiddleContent">
                    <div id="studentContentHeader">Edit Schedule</div>
                    <div id="studentTableContatiner">
                        <?php if ($result == "DONE") { ?>
                            <form name="editSchedule" action="../PhpIncludeFiles/FormProcessor/Schedule/edit_form_processor.php" method="POST">
                                <table class="formTable">
                                    <tr class="formTableRow">
                                        <td class="formTableColLabel">Class :</td>
                                        <td class="formTableColField"><input type="text" name="txtClass" value="<?php echo $roll ?>" readonly="yes" /></td>
                                        <td class="formTableColHelp"></td>
                                    </tr>
                                    <tr><td colspan="3" style="height: 10px"></td></tr>
                                    <tr class="formTableRow">
                                        <td class="formTableColLabel">Date (DD/MM/YYYY):</td>
                                        <td class="formTableColField">
                                            <input type="text" id="txtDate" name="txtDate" value="<?php echo $last_date ?>"/>
                                        </td>
                                        <td class="formTableColHelp">Must be in DD/MM/YYYY format. e.g., 25-11-2011</td>
                                    </tr>
                                    <tr><td colspan="3" style="height: 10px"></td></tr>
                                    <tr><td colspan="3" style="height: 10px"></td></tr>
                                    <tr>
                                        <td colspan="2" style="height: 10px;text-align: right"><input type="button" value="Update" name="buttonSubmit" onclick="submitForEdition()" /></td>
                                        <td></td>    
                                    </tr>
                                </table>
                            </form>
                            <?php
                        } else {
                            echo '<b>NO DATA EXIST!!!!!!</b>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div id="adminFooter">
                <?php include '../PhpIncludeFiles/Admin/AdminFooter.php'; ?>
            </div>
        </div>
    </body>
</html>
