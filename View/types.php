<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Types of Surgery</title>
    <style>
        /* General Styling */
        body, html {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* Form Container */
        .form-container {
            width: 90%;
            max-width: 800px;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        label {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
        }

        select, input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f9f9f9;
            font-size: 1em;
            color: #333;
            transition: border 0.3s;
            box-sizing: border-box;
        }

        input[type="radio"] {
            margin-right: 10px;
        }

        .radio-group {
    display: flex;
    gap: 15px;
    padding: 8px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow-x: auto;
    white-space: nowrap;
}

.radio-option {
    display: inline-flex;
    align-items: center; /* Align text vertically with the radio button */
    gap: 5px;
    vertical-align: middle; /* Ensures text is aligned with the button */
}

.radio-option label {
    font-weight: 600;
    margin: 0; /* Remove any default margin that might affect alignment */
    line-height: 1; /* Ensure consistent spacing */
}

.surgery-info h4 {
    margin-top: 20px;
    font-size: 1.1em;
    color: #333;
}

        .surgery-info {
            display: none;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            background-color: #f2f2f2;
            margin-bottom: 20px;
        }

        #comments-section {
            display: none;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            background-color: #f9f9f9;
        }

        /* Button Styling */
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

        /* Footer */
        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1em;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home.php" class="logo">Bravo</a>
    </div>

    <!-- Content Section -->
    <div class="content">
        <div class="form-container">
        <h2>Types of surgery</h2>

       

    <form action="/complication.php">
        <label for="surg">Choose a surgery:</label>
        <select name="surg" id="surg">
            <option style="display: none;">Choose</option>
            <option value="Sleeve">Sleeve</option>
            <option value="Plication">Plication</option>
            <option value="Gastric">Gastric band book</option>
            <option value="SASI">SASI</option>
            <option value="RNY">RNY</option>
            <option value="OAGB">OAGB</option>
            <option value="Sadi">Sadi</option>
            <option value="Duodenum">Duodenum switch</option>
            <option value="redo">Redo Surgery</option>
        </select>
        <br><br>
   

    <!-- Sleeve Surgery Information -->
    <div id="Sleeve" class="surgery-info common-sleeve-options">
        <h4>Type of Staplers:</h4>
        <div class="radio-group">
        <input type="radio" id="Stapler1" name="Stapler" value="Johnson">
        <label for="Stapler1"> Johnson</label><br>
        <input type="radio" id="Stapler2" name="Stapler" value="Med Tronic">
        <label for="Stapler2"> Med Tronic</label><br>
        <input type="radio" id="Stapler3" name="Stapler" value="Easy surj">
        <label for="Stapler3"> Easy surj</label><br>
        <input type="radio" id="Stapler4" name="Stapler" value="Pancer">
        <label for="Stapler4"> Pancer</label><br>
        <input type="radio" id="Stapler5" name="Stapler" value="Feing">
        <label for="Stapler5"> Feing</label><br>
        <input type="radio" id="Stapler6" name="Stapler" value="Touch Stone">
        <label for="Stapler6"> Touch Stone</label><br>
        <input type="radio" id="Stapler7" name="Stapler" value="Vector">
        <label for="Stapler7"> Vector</label><br>
        <input type="radio" id="Stapler8" name="Stapler" value="Others">
        <label for="Stapler8"> Others</label><br>
        <br>
        </div>
        <label for="number_of_stapler"><h4> Number of Staplers:</h4></label>
        <input type="number" id="number_of_stapler" name="number_of_stapler" value="Number of Staplers"min="1">
        <br>

        <h4>Color of Staplers</h4>
        <label for="Color_of_stapler"> Black:</label>
        <input type="number" id="Black" name="Color_of_stapler" value="Black "><br>
        <label for="Color_of_stapler"> Green:</label>
        <input type="number" id="Green" name="Color_of_stapler" value="Green "><br>
        <label for="Color_of_stapler"> Yellow:</label>
        <input type="number" id="Yellow" name="Color_of_stapler" value="Yellow "><br>
        <label for="Color_of_stapler"> Blue:</label>
        <input type="number" id="Blue" name="Color_of_stapler" value="Blue "><br>
        <label for="Color_of_stapler"> Purple:</label>
        <input type="number" id="Purple" name="Color_of_stapler" value="Purple "><br>
        <label for="Color_of_stapler"> Tan:</label>
        <input type="number" id="Tan" name="Color_of_stapler" value="Tan "><br>

        <h4>Type of Reinforcement</h4>
        <div class="radio-group">
        <input type="radio" id="Switcher" name="reinforcement" value="Switcher">
        <label for="Switcher">Switcher</label><br>
        <input type="radio" id="Envagination" name="reinforcement" value="Envagination">
        <label for="Envagination">Envagination</label><br>
        <input type="radio" id="Sotcher" name="reinforcement" value="Sotcher">
        <label for="Sotcher">Sotcher Full Through</label><br>
        <input type="radio" id="Butrising" name="reinforcement" value="Butrising_Material">
        <label for="Butrising">Butrising Material</label><br>
        <input type="radio" id="glue" name="reinforcement" value="Glue">
        <label for="glue">Glue</label><br>
        </div>

        <h4>Gastric Tube Fixation</h4>
        <div class="radio-group">
        <input type="radio" id="NoFixation" name="fixation" value="No">
        <label for="NoFixation">No</label><br>
        <input type="radio" id="Omenopixy" name="fixation" value="Omenopixy">
        <label for="Omenopixy">Omenopixy</label><br>
        <input type="radio" id="Peseroe" name="fixation" value="Peseroe_Fixation">
        <label for="Peseroe">Peseroe Fixation</label><br>
        <input type="radio" id="none" name="fixation" value="None">
        <label for="none">None</label><br>
        </div>

        <h4>Hiatus Hernia</h4>
        <div class="radio-group">
        <input type="radio" id="NoHernia" name="hernia" value="No">
        <label for="NoHernia">No</label><br>
        <input type="radio" id="y" name="hernia" value="Yes without Correction">
        <label for="y">Yes without Correction</label><br>
        <input type="radio" id="yc" name="hernia" value="Yes with Correction">
        <label for="yc">Yes with Correction</label><br>
        </div>

        <h4>Other Surgery</h4>
        <div class="radio-group">
        <input type="radio" id="Colesectomy" name="other_surgery" value="Colesectomy">
        <label for="Colesectomy">Colesectomy</label><br>
        <input type="radio" id="Ventral" name="other_surgery" value="Hernia">
        <label for="Ventral">Ventral Hernia</label><br>
        <input type="radio" id="Others" name="other_surgery" value="Others">
        <label for="Others">Others</label><br>
        </div>

        <div id="other-surgery-text">
            <label for="other-surgery-details"><h4>Please specify:</h4></label>
            <input type="text" id="other-surgery-details" name="other_surgery_details">
        </div>
    </div>

    <!-- SASI Surgery Information -->
    <div id="SASI" class="surgery-info">
        <h4>Estoma</h4>
        <label for="estoma-size">Size:</label>
        <input type="text" id="estoma-size" name="estoma_size"><br>
        <label for="estoma-color">Color:</label>
        <input type="text" id="estoma-color" name="estoma_color"><br>
        
        <h4>Length of Bypassed Intestine</h4>
        <input type="number" id="bypassed-intestine" name="bypassed_intestine"><br>

        <h4>Length of Whole Intestine</h4>
        <input type="number" id="whole-intestine-sasi" name="whole_intestine_sasi"><br>
    </div>
       <!-- RNY Surgery Information -->
       <div id="RNY" class="surgery-info">
        <h4>Estoma</h4>
        <label for="estoma-size">Size:</label>
        <input type="text" id="estoma-size" name="estoma_size"><br>
        <label for="estoma-color">Color:</label>
        <input type="text" id="estoma-color" name="estoma_color"><br>

        <h4>Length of Roux Limb</h4>
        <input type="number" id="rgux-limb" name="rgux_limb"><br>

        <h4>Length of Perial Limb</h4>
        <input type="number" id="perial-limb" name="perial_limb"><br>

        <h4>Length of Whole Intestine</h4>
        <input type="number" id="whole-intestine-rny" name="whole_intestine_rny"><br>

        <h4>Closure of Defects</h4>
        <div class="radio-group">
        <input type="radio" id="none-defect" name="closure_defect" value="None">
        <label for="none-defect">None</label><br>
        <input type="radio" id="peterson" name="closure_defect" value="Peterson's Defect">
        <label for="peterson">Peterson's Defect</label><br>
        <input type="radio" id="jejuno" name="closure_defect" value="Jejunojejunostomy Defect">
        <label for="jejuno">Jejunojejunostomy Defect</label><br>
        </div>
    </div>

    <!-- Redo Surgery Information -->
<div id="redo" class="surgery-info">
    <h4>Index Surgery</h4>
    <label for="index-surgery-type">Type:</label>
    <input type="text" id="index-surgery-type" name="index_surgery_type" placeholder="Enter type of surgery"><br>

    <label for="index-surgery-time">Time:</label>
    <input type="text" id="index-surgery-time" name="index_surgery_time" placeholder="Enter time of surgery"><br>

    <h4>Type of Redo</h4>
    <label for="type-of-redo">Please specify:</label>
    <input type="text" id="type-of-redo" name="type_of_redo" placeholder="Enter type of redo"><br>
</div>


    <div id="comments-section">
        <h4>Comments:</h4>
        <textarea id="comments" name="comments"></textarea>
    </div>

    <br><br>
    <button type="submit" onclick="window.location.href='complication.php'">Submit</button>
</form>
</section>

<script>
    document.getElementById('surg').addEventListener('change', function () {
        var surgeryType = this.value;
        var sections = document.querySelectorAll('.surgery-info');
        var commentsSection = document.getElementById('comments-section');

        // Hide all surgery sections initially
        sections.forEach(function (section) {
            section.style.display = 'none';
        });

        // Always show Sleeve options for SASI, SADI, RNY, OAGB, and Redo
        if (surgeryType === 'Sleeve' || surgeryType === 'SASI' || surgeryType === 'Sadi' || surgeryType === 'RNY' || surgeryType === 'OAGB') {
            document.getElementById('Sleeve').style.display = 'block';
        }

        // Show specific surgery section for SASI
        if (surgeryType === 'SASI') {
            document.getElementById('SASI').style.display = 'block';
        }
        
        // Show specific surgery section for RNY
        if (surgeryType === 'RNY') {
            document.getElementById('RNY').style.display = 'block';
        }
        
        // Show specific surgery section for OAGB and SADI
        if (surgeryType === 'OAGB' || surgeryType === 'Sadi') {
            document.getElementById('SASI').style.display = 'block'; // Show SASI options as well
            document.getElementById('Sleeve').style.display = 'block'; // Show Sleeve options
        }
        
        // Show specific surgery section for redo
        if (surgeryType === 'redo') {
            document.getElementById('redo').style.display = 'block'; // Show Redo options
        }

        // Show comments section for any surgery type selected
        if (surgeryType !== 'Choose') {
            commentsSection.style.display = 'block';
        } else {
            commentsSection.style.display = 'none';
        }
    });

    document.getElementById('Others').addEventListener('change', function () {
        var otherSurgeryText = document.getElementById('other-surgery-text');
        if (this.checked) {
            otherSurgeryText.style.display = 'block';
        } else {
            otherSurgeryText.style.display = 'none';
        }
    });
</script>

</body>
</html>
