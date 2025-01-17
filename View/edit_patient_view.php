<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient</title>
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

        .container {
            padding-top: 100px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 100%;
        }

        #editModal {
    display: none;
    position: fixed;
    z-index: 1001;
    left: 0;
    width: 100%;
    height: 80%;
    overflow: auto;
    background-color: rgba(0,0,0,0.6); /* Darker background overlay */
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border: none;
    width: 50%;
    max-width: 600px;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Softer shadow */
}

.modal-content h2 {
    margin-bottom: 20px;
    font-size: 1.8em;
    color: #333;
    text-align: center;
}

.modal-content label {
    display: block;
    margin-top: 15px;
    font-weight: 500;
    color: #555;
}

.modal-content input[type="text"],
.modal-content input[type="tel"],
.modal-content input[type="date"],
.modal-content select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 1em;
    background-color: #f9f9f9;
    transition: border-color 0.3s;
}

.modal-content input:focus {
    border-color: #3366ff;
    outline: none;
}

.modal-content button {
    width: 100%;
    padding: 10px;
    margin-top: 20px;
    background-color: #3366ff;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 1em;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s;
}

.modal-content button:hover {
    background-color: #254eda;
}

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        table {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            overflow: hidden;
        }
        
        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #3366ff;
            color: white;
            font-weight: bold;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        button {
            padding: 8px 16px;
            background-color: #3366ff;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #254eda;
        }

        .footer {
            background-color: #3366ff;
            color: white;
            padding: 20px;
            text-align: center;
        }

        /* Modal Styling */
        #editModal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <div class="navbar">
        <a href="admin_home_view.php" class="logo">Bravo</a>
    </div>

    <div class="container">
        <h1>Edit Patient</h1>

        <!-- Table to display patients -->
        <table id="patientsTable">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Birth Date</th>
                    <th>Gender</th>
                    <th>Mobile Number</th>
                    <th>Hospital Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="footer">
        <p>Contact Us: BRAVO@ngo.org | © 2024 Bravo</p>
    </div>

    <!-- Modal for editing patient details -->
    <div id="editModal">
        <div class="modal-content">
            <span onclick="closeEditForm()" class="close">&times;</span>
            <h2>Edit Patient Details</h2>
            <form id="editPatientForm">
                <input type="hidden" id="patient_id">
                <label>Full Name: <input type="text" id="full_name" required></label><br>
                <label>Birth Date: <input type="date" id="birth_date" required></label><br>
                <label>Gender: <input type="text" id="gender" required></label><br>
                <label>Mobile Number: <input type="tel" id="mobile_number" required></label><br>
                <label>Hospital Name: <input type="text" id="hospital_name" required></label><br>
                <label>BMI Value: <input type="text" id="bmi_value" required></label><br>
                <button type="button" onclick="submitEditForm()">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function fetchPatients() {
    fetch('../Controller/edit_patient_controller.php?action=getPatients')
        .then(response => response.json())
        .then(patients => {
            const tableBody = document.querySelector("#patientsTable tbody");
            tableBody.innerHTML = "";
            patients.forEach(patient => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <td>${patient.full_name}</td>
                    <td>${patient.birth_date}</td>
                    <td>${patient.gender}</td>
                    <td>${patient.mobile_number}</td>
                    <td>${patient.hospital_name}</td>
                    <td><button onclick="openEditForm(${patient.patient_id})">Edit</button></td>
                `;
                tableBody.appendChild(row);
            });
        });
}

function openEditForm(patientId) {
    console.log("Fetching data for patient ID:", patientId);

    // Fetch current patient data before opening the form
    fetch(`../Controller/edit_patient_controller.php?action=getPatientById&patient_id=${patientId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error("Network response was not ok " + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            // Log and alert the data as a string for debugging
            console.log("Received data:", JSON.stringify(data, null, 2));
            //alert("Received data:\n" + JSON.stringify(data, null, 2));
            //alert("Patient ID: " + patientId);
            // Populate form fields with the existing data
            if (data) {
                document.getElementById('patient_id').value = patientId;
                document.getElementById('full_name').value = data.FullName || '';
                document.getElementById('birth_date').value = data.BirthDate	 || '';
                document.getElementById('gender').value = data.Gender	 || '';
                document.getElementById('mobile_number').value = data.MobileNumber	 || '';
                document.getElementById('hospital_name').value = data.HospitalName	 || '';
                document.getElementById('bmi_value').value = data.bmi_value || '';

                // Show the modal only after data is populated
                document.getElementById('editModal').style.display = 'block';
            } else {
                alert("Error: No valid data found for this patient.");
            }
        })
        .catch(error => {
            console.error("Error fetching patient data:", error);
            alert("Error fetching patient details. Please try again.");
        });
}

function closeEditForm() {
    document.getElementById('editModal').style.display = 'none';
}

function submitEditForm() {
    const patientData = {
        patient_id: document.getElementById('patient_id').value,
        full_name: document.getElementById('full_name').value,
        birth_date: document.getElementById('birth_date').value,
        gender: document.getElementById('gender').value,
        mobile_number: document.getElementById('mobile_number').value,
        hospital_name: document.getElementById('hospital_name').value,
        bmi_value: document.getElementById('bmi_value').value
    };

    console.log("Data to be sent:", patientData);

    fetch('../Controller/edit_patient_controller.php?action=updatePatient&patient_id=' + patientData.patient_id, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(patientData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Patient data updated successfully');
            closeEditForm();
            fetchPatients();
        } else {
            alert('Failed to update patient data');
        }
    })
    .catch(error => console.error("Error updating patient:", error));
}

window.onload = fetchPatients;
    </script>
</body>
</html>
