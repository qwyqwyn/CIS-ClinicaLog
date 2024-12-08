document.getElementById("staffForm").addEventListener("input", function() {
    sessionStorage.setItem("lastName", document.getElementById("lastName").value);
    sessionStorage.setItem("firstName", document.getElementById("firstName").value);
    sessionStorage.setItem("middleName", document.getElementById("middleName").value);
    sessionStorage.setItem("dob", document.getElementById("dob").value);
    sessionStorage.setItem("sex", document.getElementById("sex").value);
    sessionStorage.setItem("facultyID", document.getElementById("facultyID").value);
    sessionStorage.setItem("college", document.getElementById("college").value);
    sessionStorage.setItem("department", document.getElementById("department").value);
    sessionStorage.setItem("role", document.getElementById("role").value);
    sessionStorage.setItem("region", document.getElementById("region").value);
    sessionStorage.setItem("province", document.getElementById("province").value);
    sessionStorage.setItem("municipality", document.getElementById("municipality").value);
    sessionStorage.setItem("barangay", document.getElementById("barangay").value);
    sessionStorage.setItem("street", document.getElementById("street").value);
    sessionStorage.setItem("email", document.getElementById("email").value);
    sessionStorage.setItem("contactNumber", document.getElementById("contactNumber").value);
    sessionStorage.setItem("emergencyContactName", document.getElementById("emergencyContactName").value);
    sessionStorage.setItem("relationship", document.getElementById("relationship").value);
    sessionStorage.setItem("emergencyContactNumber", document.getElementById("emergencyContactNumber").value);
});
 
window.addEventListener("load", function() {
    if (sessionStorage.getItem("lastName")) {
        document.getElementById("lastName").value = sessionStorage.getItem("lastName");
    }
    if (sessionStorage.getItem("firstName")) {
        document.getElementById("firstName").value = sessionStorage.getItem("firstName");
    }
    if (sessionStorage.getItem("middleName")) {
        document.getElementById("middleName").value = sessionStorage.getItem("middleName");
    }
    if (sessionStorage.getItem("dob")) {
        document.getElementById("dob").value = sessionStorage.getItem("dob");
    }
    if (sessionStorage.getItem("sex")) {
        document.getElementById("sex").value = sessionStorage.getItem("sex");
    }
    if (sessionStorage.getItem("facultyID")) {
        document.getElementById("facultyID").value = sessionStorage.getItem("facultyID");
    }
    if (sessionStorage.getItem("college")) {
        document.getElementById("college").value = sessionStorage.getItem("college");
    }
    if (sessionStorage.getItem("department")) {
        document.getElementById("department").value = sessionStorage.getItem("department");
    }
    if (sessionStorage.getItem("role")) {
        document.getElementById("role").value = sessionStorage.getItem("role");
    }
    if (sessionStorage.getItem("region")) {
        document.getElementById("region").value = sessionStorage.getItem("region");
    }
    if (sessionStorage.getItem("province")) {
        document.getElementById("province").value = sessionStorage.getItem("province");
    }
    if (sessionStorage.getItem("municipality")) {
        document.getElementById("municipality").value = sessionStorage.getItem("municipality");
    }
    if (sessionStorage.getItem("barangay")) {
        document.getElementById("barangay").value = sessionStorage.getItem("barangay");
    }
    if (sessionStorage.getItem("street")) {
        document.getElementById("street").value = sessionStorage.getItem("street");
    }
    if (sessionStorage.getItem("email")) {
        document.getElementById("email").value = sessionStorage.getItem("email");
    }
    if (sessionStorage.getItem("contactNumber")) {
        document.getElementById("contactNumber").value = sessionStorage.getItem("contactNumber");
    }
    if (sessionStorage.getItem("emergencyContactName")) {
        document.getElementById("emergencyContactName").value = sessionStorage.getItem("emergencyContactName");
    }
    if (sessionStorage.getItem("relationship")) {
        document.getElementById("relationship").value = sessionStorage.getItem("relationship");
    }
    if (sessionStorage.getItem("emergencyContactNumber")) {
        document.getElementById("emergencyContactNumber").value = sessionStorage.getItem("emergencyContactNumber");
    }
});
