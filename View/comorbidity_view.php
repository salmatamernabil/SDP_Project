<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-Morbidities Status</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }
        .navbar .logo {
            color: #3366ff;
            font-size: 2em;
            font-weight: 800;
            text-decoration: none;
            transition: transform 0.3s;
        }
        .navbar .logo:hover {
            transform: scale(1.1);
        }

        /* Padding to prevent overlap with fixed navbar */
        .content {
            padding-top: 125px;
            padding-bottom: 35px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
        }

        .form-container {
            background-color: white;
            border-radius: 12px;
            padding: 40px;
            width: 450px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-group, form {
            margin-bottom: 20px;
        }

        .form-group input[type="radio"] {
            margin-right: 10px;
        }

        .form-group input[type="number"],
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1em;
            background-color: #f9f9f9;
            color: #333;
            transition: border 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3366ff;
        }

        label {
            font-size: 1em;
            color: #333;
        }

        button {
            width: 100%;
            padding: 15px;
            background-color: #3366ff;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.2em;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        button:hover {
            background-color: #254eda;
            transform: scale(1.02);
        }

        /* Footer Styling */
        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
        }

        /* Enhance Layout */
        .functional_status, .diabetes, .treatment, .hypertension, .lipid, .reflux, .fatty, .gynecological, .fertility, .PCEO, .smoking, .alcoholization, .addict, .others {
            margin-bottom: 25px;
        }

        /* Add spacing between radio options */
        .form-group input[type="radio"] {
            margin: 10px 0;
        }

        .form-group textarea {
            height: 120px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

  <!-- Content Section -->
<div class="content">

    <div class="form-container">
    <h2>Co-Morbidities Status</h2>
    <form action="../Controller/comorbidity_controller.php" method="POST">
        <!-- Hidden input for Patient ID -->
        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($_POST['patient_id'] ?? ''); ?>">

      <!-- Functional Status -->
      <div class="functional_status">
        <h4>Functional Status:</h4>
        <input type="radio" id="dis" name="functional_status" value="Disabled" <?php echo ($_POST['functional_status'] ?? '') == 'Disabled' ? 'checked' : ''; ?>>
        <label for="dis">Disabled</label><br>
        <input type="radio" id="one_floor" name="functional_status" value="one_floor" <?php echo ($_POST['functional_status'] ?? '') == 'one_floor' ? 'checked' : ''; ?>>
        <label for="one_floor">One floor</label><br>
        <input type="radio" id="3_floors" name="functional_status" value="3_floors" <?php echo ($_POST['functional_status'] ?? '') == '3_floors' ? 'checked' : ''; ?>>
        <label for="3_floors">3 floors or more</label>
    </div>

    <div class="diabetes">

        <h4>Diabetes:</h4>
          <input type="radio" id="yes" name="diabetes" value="yes">
          <label for="yes">Yes</label><br>
          <input type="radio" id="no" name="diabetes" value="no">
          <label for="no">No</label><br>

          <label for="habic">Last HABIC:</label>
          <input type="number" id="habic" name="habic">

    </div>

        <!-- Duration -->
        <div id="durationDiv">
        <p></p>
        <label for="duration">Enter duration:</label>
        <input type="number" id="duration" name="diabetes_duration" value="<?php echo htmlspecialchars($_POST['diabetes_duration'] ?? ''); ?>">
    </div>

        <div class="Treatment">
          
            <h4>Treatment:</h4>
              <input type="radio" id="ins" name="Treatment" value="ins">
              <label for="ins">Insulin</label><br>
              <input type="radio" id="life" name="Treatment" value="life">
              <label for="life">Life Style</label><br>
              <input type="radio" id="oral" name="Treatment" value="oral">
              <label for="oral">Oral</label><br>
              <input type="radio" id="both" name="Treatment" value="both">
              <label for="both">Both</label><br>
        </div>


  

       <!-- Hypertension -->
       <div class="hypertension">
    <h4>Hypertension:</h4>
    <input type="radio" id="hypertension_yes" name="hypertension" value="yes" <?php echo ($_POST['hypertension'] ?? '') == 'yes' ? 'checked' : ''; ?>>
    <label for="hypertension_yes">Yes</label><br>
    <input type="radio" id="hypertension_no" name="hypertension" value="no" <?php echo ($_POST['hypertension'] ?? '') == 'no' ? 'checked' : ''; ?>>
    <label for="hypertension_no">No</label><br>
</div>



    <div id="treatment_radio">

            <h4>Treatment:</h4>

        <input type="radio" id="yest" name="Treatment" value="yes">
          <label for="yest">Yes</label><br>
          <input type="radio" id="not" name="Treatment" value="no">
          <label for="not">No</label><br>


    </div>


        <!-- Lipid Profile -->
        <div class="lipid_profile">
    <h4>Lipid Profile:</h4>
    <input type="radio" id="lipid_hyper" name="lipid_profile" value="Hyperchloesterilenia" <?php echo ($_POST['lipid_profile'] ?? '') == 'Hyperchloesterilenia' ? 'checked' : ''; ?>>
    <label for="lipid_hyper">Hyperchloesterilenia</label><br>
    <input type="radio" id="lipid_no" name="lipid_profile" value="no" <?php echo ($_POST['lipid_profile'] ?? '') == 'no' ? 'checked' : ''; ?>>
    <label for="lipid_no">None</label><br>
</div>

          

  <!-- Reflux -->
  <div class="reflux">
    <h4>Reflux:</h4>
    <input type="radio" id="reflux_no" name="reflux" value="No" <?php echo ($_POST['reflux'] ?? '') == 'No' ? 'checked' : ''; ?>>
    <label for="reflux_no">No</label><br>
    <input type="radio" id="reflux_intermediate_without" name="reflux" value="Intermediate_Without_Treatment" <?php echo ($_POST['reflux'] ?? '') == 'Intermediate_Without_Treatment' ? 'checked' : ''; ?>>
    <label for="reflux_intermediate_without">Intermediate Without Treatment</label><br>
    <input type="radio" id="reflux_intermediate_with" name="reflux" value="Intermediate_With_Treatment" <?php echo ($_POST['reflux'] ?? '') == 'Intermediate_With_Treatment' ? 'checked' : ''; ?>>
    <label for="reflux_intermediate_with">Intermediate With Treatment</label><br>
</div>





    <!-- Fatty Liver -->
<div class="Faty">
    <h4>Fatty Liver:</h4>

    <h4>Size:</h4>
    <input type="radio" id="Mild" name="fatty_liver" value="Mild" <?php echo ($_POST['fatty_liver'] ?? '') == 'Mild' ? 'checked' : ''; ?>>
    <label for="Mild">Mild</label><br>
    <input type="radio" id="Moderate" name="fatty_liver" value="Moderate" <?php echo ($_POST['fatty_liver'] ?? '') == 'Moderate' ? 'checked' : ''; ?>>
    <label for="Moderate">Moderate</label><br>
    <input type="radio" id="Severe" name="fatty_liver" value="Severe" <?php echo ($_POST['fatty_liver'] ?? '') == 'Severe' ? 'checked' : ''; ?>>
    <label for="Severe">Severe</label><br>

    <h4>Liver Enzymes:</h4>
    <input type="radio" id="elve" name="liver_enzymes" value="Elevated" <?php echo ($_POST['liver_enzymes'] ?? '') == 'Elevated' ? 'checked' : ''; ?>>
    <label for="Elevated">Elevated</label><br>
    <input type="radio" id="noelve" name="liver_enzymes" value="Not Elevated" <?php echo ($_POST['liver_enzymes'] ?? '') == 'Not Elevated' ? 'checked' : ''; ?>>
    <label for="Not Elevated">Not Elevated</label><br>
</div>

<!-- Gynecological -->
<div class="gynecological">
    <h4>Menstrual cycle:</h4>
    <input type="radio" id="menstrual_regular" name="gynecological" value="Regular" <?php echo ($_POST['gynecological'] ?? '') == 'Regular' ? 'checked' : ''; ?>>
    <label for="menstrual_regular">Regular</label><br>
    <input type="radio" id="menstrual_irregular" name="gynecological" value="Irregular" <?php echo ($_POST['gynecological'] ?? '') == 'Irregular' ? 'checked' : ''; ?>>
    <label for="menstrual_irregular">Irregular</label><br>

    <h4>Menopause:</h4>
    <input type="radio" id="menopause_yes" name="menopause" value="Yes" <?php echo ($_POST['menopause'] ?? '') == 'Yes' ? 'checked' : ''; ?>>
    <label for="menopause_yes">Yes</label><br>
    <input type="radio" id="menopause_no" name="menopause" value="No" <?php echo ($_POST['menopause'] ?? '') == 'No' ? 'checked' : ''; ?>>
    <label for="menopause_no">No</label><br>

    <h4>Uterus Removed:</h4>
    <input type="radio" id="uterus_removed_yes" name="uterus_removed" value="Yes" <?php echo ($_POST['uterus_removed'] ?? '') == 'Yes' ? 'checked' : ''; ?>>
    <label for="uterus_removed_yes">Yes</label><br>
    <input type="radio" id="uterus_removed_no" name="uterus_removed" value="No" <?php echo ($_POST['uterus_removed'] ?? '') == 'No' ? 'checked' : ''; ?>>
    <label for="uterus_removed_no">No</label><br>
</div>


  <div class="fertility">

        <h4>Fertility</h4>
        <input type="radio" id="Fertile" name="Fertility" value="Fertile">
          <label for="Fertile">Fertile</label><br>

          <input type="radio" id="PI" name="Fertility" value="Primary Infertility">
          <label for="Primary Infertility">Primary Infertility </label><br>

          <input type="radio" id="SI" name="Fertility" value="seceondry Infertility">
          <label for="seceondry Infertility">seceondry Infertility </label><br>

          <input type="radio" id="NM" name="Fertility" value="Not Married">
          <label for="Not Married">Not Married </label><br> 
  </div>

  <div class="PCEO">

   
        <h4>PCEO:</h4>
        <input type="radio" id="NOp" name="PCEO" value="NO">
          <label for="NO">NO</label><br>

          <input type="radio" id="YNT" name="PCEO" value="Yes Without Treatment">
          <label for="Yes Without Treatment">Yes Without Treatment </label><br>

          <input type="radio" id="YWT" name="PCEO" value="Yes With Treatment">
          <label for="Yes With Treatment">Yes With Treatment </label><br>

          <input type="radio" id="None" name="PCEO" value="None">
          <label for="None">None </label><br>

  </div>
  <div class="Smoking">
    
        <h4>Smoking:</h4>
        <input type="radio" id="Smoker" name="Smoking" value="Smoker">
          <label for="Smoker">Smoker</label><br>
          <input type="radio" id="EX " name="Smoking" value="EX Smoker">
          <label for="EX Smoker">EX Smoker </label><br> 
          <input type="radio" id="None_Smoker" name="Smoking" value="None Smoker">
          <label for="None Smoker">None Smoker </label><br>
  </div>
  <div class="Alcoholization">
  
        <h4>Alcoholization:</h4>
        <input type="radio" id="YesAl" name="Alcoholization" value="Yes">
          <label for="Yes">Yes</label><br>
          <input type="radio" id="NoAl" name="Alcoholization" value="No">
          <label for="No">No </label><br> 
  </div>
  <div class="Addict">
   
        <h4>Addict:</h4>
        <input type="radio" id="YesA" name="Addict" value="Yes">
          <label for="Yes">Yes</label><br>
          <input type="radio" id="NoA" name="Addict" value="No">
          <label for="No">No </label><br> 
  </div>
 <div class="Others">
    <h4>Comments:</h4>
    <textarea></textarea>
 </div>
</section>




                <!-- Submit Button -->
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

</body>
</html>