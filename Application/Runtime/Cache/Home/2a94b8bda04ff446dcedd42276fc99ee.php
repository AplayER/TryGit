<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>

<title>TEST</title>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" >
<script type="text/javascript" src="/TryGit/Public/js/jquery-1.7.1.min.js"></script>
<style>

/* Add some margin to the page and set a default font */

body {
  margin: 30px;
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
}


/* Style the form with a coloured background (and a gradient for Gecko/WebKit browsers), along with curved corners and a drop shadow */

form {
  width: 35em;
  margin: 0 auto;
  padding: 50px 60px;
  overflow: auto;
  color: #3e4a49;
  background-color: #f5eedb;
  background: -webkit-gradient( linear, left bottom, left top, color-stop(0,#f5eedb), color-stop(1, #faf8f1) );
  background: -moz-linear-gradient( center bottom, #f5eedb 0%, #faf8f1 100% );  
  border-radius: 10px;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;  
  box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  -moz-box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  -webkit-box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
}


/* Give form elements consistent margin, padding and line height */

form ul {
  list-style: none;
  margin: 0;
  padding: 0;
}

form ul li {
  margin: .9em 0 0 0;
  padding: 0;
}

form * {
  line-height: 1em;
}


/* Form heading */

form h1 {
  margin: 0 0 1.5em 0;
  padding: 0;
  text-align: center;
}


/* Give each fieldset a darker background, dark curved border and plenty of space */

fieldset {
  padding: 0 20px 20px;
  margin: 0 0 30px;
  border: 2px solid #593131;
  background: #eae1c0;
  border-radius: 10px;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;
}


/* Give each fieldset legend a nice curvy green box with white text */

legend {
  color: #fff;
  background: #8fb98b;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 0.9em;
  font-weight: bold;
  text-align: center;
  padding: 5px;
  margin: 0;
  width: 9em;
  border: 2px solid #593131;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
}


/* The field labels */

label {
  display: block;
  float: left;
  clear: left;
  text-align: right;
  width: 40%;
  padding: .4em 0 0 0;
  margin: .15em .5em 0 0;
}


/* Style the fields */

input, select, textarea {
  display: block;
  margin: 0;
  padding: .4em;
  width: 50%;
}

input, textarea, .date {
  border: 2px solid #eae1c0;
  border-radius: 5px;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;    
  box-shadow: rgba(0,0,0,.5) 1px 1px 1px 1px inset;
  -moz-box-shadow: rgba(0,0,0,.5) 1px 1px 1px 1px inset;
  -webkit-box-shadow: rgba(0,0,0,.5) 1px 1px 1px 1px inset;
  background: #fff;
}

input {
  font-size: .9em;
}

select {
  padding: 0;
  margin-bottom: 2.5em;
  position: relative;
  top: .7em;
}

textarea {
  font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
  font-size: .9em;
  height: 5em;
}


/* Place a border around focused fields, and hide the inner shadow */

form *:focus {
  border: 2px solid #593131;
  outline: none;
  box-shadow: none;
  -moz-box-shadow: none;
  -webkit-box-shadow: none;
}


/* Display correctly filled-in fields with a green background */

input:valid, textarea:valid {
  background: #efe;
}


/* Submit button */

input[type="submit"] {
  margin: 50px auto 0 auto;
  width: 12em;
  padding: 10px;
  border: 2px solid #593131;
  border-radius: 10px;
  -moz-border-radius: 10px;
  -webkit-border-radius: 10px;  
  box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  -moz-box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  -webkit-box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  color: #fff;
  background: #593131;
  font-size: 1.2em;
  font-weight: bold;
  -webkit-appearance: none;
}

input[type="submit"]:hover, input[type="submit"]:active {
  cursor: pointer;
  background: #fff;
  color: #593131;
}

input[type="submit"]:active {
  background: #eee;
  box-shadow: 0 0 .5em rgba(0, 0, 0, .8) inset;
  -moz-box-shadow: 0 0 .5em rgba(0, 0, 0, .8) inset;
  -webkit-box-shadow: 0 0 .5em rgba(0, 0, 0, .8) inset;
}


/* Header/footer boxes */

.wideBox {
  clear: both;
  text-align: center;
  margin: 70px;
  padding: 10px;
  background: #ebedf2;
  border: 1px solid #333;
  line-height: 80%;
}

.wideBox h1 {
  font-weight: bold;
  margin: 20px;
  color: #666;
  font-size: 1.5em;
}


/* Validator error boxes */

.error {
  background-color: #fffe36;
  border: 1px solid #e1e16d;
  font-size: .8em;
  color: #000;
  padding: .3em;
  margin-left: 5px;
  border-radius: 5px; 
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  -box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  -moz-box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
  -webkit-box-shadow: 0 0 .5em rgba(0, 0, 0, .8);
}

</style>

<!--[if IE]>
<style>

/* Work around IE fieldset background bleed bug */

fieldset {
  background: transparent;
}

</style>
<![endif]--> 


<!--[if IE 7]>
<style>

/* Work around broken IE7 box model */

form ul li {
  margin: 0;
}

label {
  padding-top: 1.3em;
}

</style>
<![endif]--> 

</head>

<body>

<div class="wideBox">
  <h1>Please don't use it in IE Browser</h1>
</div>

<form id="orderForm" action="/TryGit/index.php/Home/Index/testExcel" method="post">

  <h1>Software Order Form</h1>

  <fieldset>
    <legend>Person details</legend>

    <ul>

      <li>
        <label for="sno"> 学号</label>
        <input type="number" name="sno" id="sno" placeholder="Just like 2015282110177" required="required" autofocus="autofocus" maxlength="50" />
      </li>

      <li>
        <label for="sname">姓名</label>
        <input type="text" name="sname" id="sname" placeholder="Just like 冯晓白" required="required" maxlength="100" />
      </li>

      <!-- <li>
        <label for="numLicenses">Number of licenses</label>
        <input type="number" name="numLicenses" id="numLicenses" placeholder="How many to buy (1-10)" required="required" min="1" max="10" maxlength="2" />
      </li> -->
      <li>
        <label for="telNumber">手机号</label>
        <input type="number" name="telNumber" id="telNumber" placeholder="Just like 13393333333" required="required"  maxlength="20" />
      </li>
      
    </ul>

  </fieldset>

  <fieldset>
    <legend>Address details</legend>

    <ul>

      <li>
        <label for="DormNum">宿舍号</label>
        <input type="text" name="DormNum" id="DormNum" placeholder="Just like B301" required="required" maxlength="50" />
      </li>

      <li>
        <label for="Address">Address(地址)</label>
        <textarea name="Address" id="Address" placeholder="House number, street, city, state" required="required" maxlength="150"></textarea>
      </li>

      <li>
        <label for="IDCardNum">身份证号</label>
        <input type="text" name="IDCardNum" id="IDCardNum" placeholder="ID Card Number" required="required" maxlength="30" />
      </li>

      <li>
        <label for="Country">Country</label>
        <select name="Country" id="Country"><option>China</option><option>Canada</option><option>New Zealand</option><option>United Kingdom</option><option>United States</option></select>
      </li>

      <li>
        <label for="phone">Phone <em>(optional)</em></label>
        <input type="tel" name="phone" id="phone" placeholder="Include country prefix, e.g. +44" maxlength="20" />
      </li>

    </ul>

  </fieldset>

  <fieldset>
    <legend>School details</legend>

    <ul>

      <li>
        <label for="teacher">导师</label>
        <input type="text" name="teacher" id="teacher" placeholder="Just Like 李晓红" required="required" maxlength="30"  />
      </li>

      <li>
        <label for="major">专业</label>
        <input type="text" name="major" id="major" placeholder="Just Like 计算机技术" required="required" maxlength="30" />
      </li>

      <li>
        <label for="Date">提交时间</label>
        <input type="month" name="Date" id="Date" placeholder="YYYY-MM" required="required" maxlength="7" />
      </li>

    </ul>

  </fieldset>

  <input type="submit" name="submit" id="submit" placeholder="Submit Your Form" />
   
</form>
</body>
</html>