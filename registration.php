<?php include 'header.php'; if(!loginCheck($session)) : ?>

<?php if(isset($_SESSION['Email'])) : ?>
Please fill in and submit the form below. <br>
Your Google email has been provided by Google and cannot be changed.<br>
Please enter an email in the "Email Address" field. This will be the address we
send emails to.
<form action="admin/registrationhandler.php" method="post">
  Google email: <?php echo $_SESSION['Email'] ?><br>
  Full Name: <input type="text" name="name"><br>
  Email Address: <input type="text" name="email"><br>
  Please tick the events for which you would like to recieve emails from us:<br>
  <input type="checkbox" name="attending" value="attending" checked=true> At the start of a roll call (allows you to say you're not attending, causing vetos)<br>
  <input type="checkbox" name="voting" value="voting" checked=true> When voting begins<br>
  <input type="checkbox" name="results" value="results" checked=true> When results are made available<br>
  <input type="submit" value="Submit">
</form>



<?php else : ?>
Please authenticate with Google. It does not matter which account you use, but you will have to use this account to vote in the future.
  <div class="g-signin2" data-onsuccess="onSignIn"></div>
<?php endif; ?>




<?php else : ?>

<p> You have successfully registered, thank you.</p>

<?php endif; ?>
