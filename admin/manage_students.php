<?php
session_start();
require_once('../db/dbConnector.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$db = new DbConnector();
$admin_id = $_SESSION['admin_id'];

// Get admin info
$query = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Admin Dashboard</title>
    
    <!-- External CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="icon" href="../images/light-logo.png">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/dashboard-shared.css">
    <style>
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-btn {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .add-btn:hover {
            background: #2980b9;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85em;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-archived {
            background: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            margin: 0 2px;
        }

        .edit-btn {
            background: #2ecc71;
            color: white;
        }

        .archive-btn {
            background: #e74c3c;
            color: white;
        }

        .modal-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-footer {
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }

        .button-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .import-btn {
            background: #28a745;
        }

        .import-btn:hover {
            background: #218838;
        }

        .file-upload-container {
            border: 2px dashed #ddd;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-container:hover {
            border-color: #3498db;
        }

        .file-upload-container i {
            font-size: 40px;
            color: #666;
            margin-bottom: 10px;
        }

        .file-upload-text {
            color: #666;
            margin-bottom: 10px;
        }

        .file-name {
            color: #3498db;
            font-weight: bold;
            margin-top: 10px;
            display: none;
        }

        .template-download {
            display: inline-block;
            margin-top: 15px;
            color: #3498db;
            text-decoration: underline;
            cursor: pointer;
        }

        #fileInput {
            display: none;
        }

        .archive-list-btn {
            background: #6c757d;
        }
        
        .archive-list-btn:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
            color: white;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: normal;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        /* Table styling */
        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        /* Action buttons */
        .btn-success { background-color: #28a745; }
        .btn-danger { background-color: #dc3545; }
        .btn-info { background-color: #17a2b8; }

        .btn:hover {
            opacity: 0.85;
        }

        .template-downloads {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .template-download {
            display: inline-flex;
            align-items: center;
            color: #3498db;
            text-decoration: none;
            gap: 5px;
        }

        .template-download:hover {
            color: #2980b9;
            text-decoration: underline;
        }

        .template-download i {
            font-size: 1.2em;
        }

        .template-downloads {
            margin-top: 15px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .template-downloads .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
        }

        .template-downloads .btn i {
            font-size: 1.1em;
        }

        .btn-outline-success:hover {
            color: white;
        }

        .btn-outline-danger:hover {
            color: white;
        }

        .template-downloads {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .btn-template {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-template.excel {
            color: #217346;
            border: 1px solid #217346;
            background-color: transparent;
        }

        .btn-template.excel:hover {
            color: white;
            background-color: #217346;
        }

        .btn-template.pdf {
            color: #DC3545;
            border: 1px solid #DC3545;
            background-color: transparent;
        }

        .btn-template.pdf:hover {
            color: white;
            background-color: #DC3545;
        }
		.help-card a{
		color: black;
		}
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
</head>
<body>
    <?php include 'includes/navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="table-container">
                <div class="header-actions">
                    <h2>Manage Students</h2>
                    <div class="button-group">
                        <button class="add-btn" onclick="showAddStudentModal()">
                            <i class="fas fa-plus"></i> Add New Student
                        </button>
                        <button class="add-btn import-btn" onclick="showImportStudentModal()">
                            <i class="fas fa-file-import"></i> Import Students
                        </button>
                        <a href="archived_students.php" class="add-btn archive-list-btn">
                            <i class="fas fa-archive"></i> Archived Accounts
                        </a>
                    </div>
                </div>

                <table id="studentsTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                ajax: {
                    url: 'handlers/student_handler.php?action=get_active_students',
                    type: 'GET'
                },
                columns: [
                    { data: 'lrn' },
                    { 
                        data: null,
                        render: function(data, type, row) {
                            return `${row.firstname} ${row.middlename ? row.middlename + ' ' : ''}${row.lastname}`;
                        }
                    },
                    { data: 'email' },
                    { 
                        data: 'status',
                        render: function(data, type, row) {
                            return `<span class="badge badge-${data === 'active' ? 'success' : 'warning'}">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary edit-btn" onclick="editStudent(${row.student_id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger archive-btn" onclick="archiveStudent(${row.student_id})">
                                    <i class="fas fa-archive"></i>
                                </button>
                            `;
                        }
                    }
                ],
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });

        function showAddStudentModal() {
            const modal = `
            <div class="modal fade" id="addStudentModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add New Student</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="addStudentForm" onsubmit="handleAddStudent(event)">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>LRN (Learner Reference Number)*</label>
                                    <input type="text" 
                                           name="lrn" 
                                           class="form-control" 
                                           required 
                                           pattern="[0-9]{12}" 
                                           maxlength="12"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           title="Please enter a valid 12-digit LRN">
                                    <small class="form-text text-muted">Enter 12 digits only</small>
                                </div>
                                <div class="form-group">
                                    <label>First Name*</label>
                                    <input type="text" name="firstname" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middlename" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Last Name*</label>
                                    <input type="text" name="lastname" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" 
                                           name="contact_number" 
                                           class="form-control" 
                                           pattern="[0-9]+" 
                                           maxlength="11">
                                </div>
                                <div class="form-group">
                                    <label>Gender*</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Student</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

            if (!document.getElementById('addStudentModal')) {
                document.body.insertAdjacentHTML('beforeend', modal);
            }
            $('#addStudentModal').modal('show');
        }

        function handleAddStudent(event) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'POST',
                data: {
                    action: 'add_student',
                    lrn: formData.get('lrn'),
                    firstname: formData.get('firstname'),
                    lastname: formData.get('lastname'),
                    middlename: formData.get('middlename') || '',
                    email: formData.get('email') || '',
                    contact_number: formData.get('contact_number') || '',
                    gender: formData.get('gender')
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                html: result.message.replace(/\n/g, '<br>'),
                                icon: 'success'
                            }).then(() => {
                                $('#addStudentModal').modal('hide');
                                $('#studentsTable').DataTable().ajax.reload();
                                document.getElementById('addStudentForm').reset();
                            });
                        } else {
                            Swal.fire('Error', result.message || 'Failed to add student', 'error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        console.error('Raw response:', response);
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    Swal.fire('Error', 'Failed to add student. Please try again.', 'error');
                }
            });
        }

        function editStudent(studentId) {
            // First fetch student details
            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'GET',
                data: {
                    action: 'get_student_details',
                    student_id: studentId
                },
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.status === 'success') {
                            showEditStudentModal(result.data);
                        } else {
                            Swal.fire('Error', result.message || 'Failed to fetch student details', 'error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to fetch student details', 'error');
                }
            });
        }

        function showEditStudentModal(student) {
            const modal = `
            <div class="modal fade" id="editStudentModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Student</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="editStudentForm" onsubmit="handleEditStudent(event, ${student.student_id})">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label>LRN (Learner Reference Number)*</label>
                                    <input type="text" 
                                           name="lrn" 
                                           class="form-control" 
                                           required 
                                           pattern="[0-9]{12}" 
                                           maxlength="12"
                                           value="${student.lrn}"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           title="Please enter a valid 12-digit LRN">
                                </div>
                                <div class="form-group">
                                    <label>First Name*</label>
                                    <input type="text" name="firstname" class="form-control" value="${student.firstname}" required>
                                </div>
                                <div class="form-group">
                                    <label>Middle Name</label>
                                    <input type="text" name="middlename" class="form-control" value="${student.middlename || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Last Name*</label>
                                    <input type="text" name="lastname" class="form-control" value="${student.lastname}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="${student.email || ''}">
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" class="form-control" value="${student.contact_number || ''}" maxlength="11">
                                </div>
                                <div class="form-group">
                                    <label>Gender*</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="Male" ${student.gender === 'Male' ? 'selected' : ''}>Male</option>
                                        <option value="Female" ${student.gender === 'Female' ? 'selected' : ''}>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

            // Remove existing modal if any
            $('#editStudentModal').remove();
            // Add new modal to body
            document.body.insertAdjacentHTML('beforeend', modal);
            // Show the modal
            $('#editStudentModal').modal('show');
        }

        function handleEditStudent(event, studentId) {
            event.preventDefault();
            const formData = new FormData(event.target);
            
            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'POST',
                data: {
                    action: 'edit_student',
                    student_id: studentId,
                    ...Object.fromEntries(formData)
                },
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            Swal.fire('Success', result.message, 'success');
                            $('#studentsTable').DataTable().ajax.reload();
                            $('#editStudentModal').modal('hide');
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update student', 'error');
                }
            });
        }

        function archiveStudent(studentId) {
            Swal.fire({
                title: 'Archive Student?',
                text: "This student will be archived and can be restored later.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'handlers/student_handler.php',
                        method: 'POST',
                        data: {
                            action: 'archive_student',
                            student_id: studentId
                        },
                        success: function(response) {
                            try {
                                const result = JSON.parse(response);
                                if (result.status === 'success') {
                                    Swal.fire('Success', result.message, 'success');
                                    $('#studentsTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Error', result.message, 'error');
                                }
                            } catch (e) {
                                Swal.fire('Error', 'Invalid server response', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Failed to archive student', 'error');
                        }
                    });
                }
            });
        }

        function showImportStudentModal() {
            const modal = `
            <div class="modal fade" id="importStudentModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Import Students</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <form id="importStudentForm" onsubmit="handleImportStudents(event)">
                            <div class="modal-body">
                                <div class="alert alert-info">
                                    <strong>Note:</strong> Please use the template provided and ensure:
                                    <ul>
                                        <li>LRN must be exactly 12 digits</li>
                                        <li>All required fields are filled</li>
                                        <li>Gender must be either "Male" or "Female"</li>
                                        <li>Save the file as CSV or XLSX format</li>
                                        <li>Make all the format to TEXT not SCIENTIFIC or etc...</li>
                                    </ul>
                                </div>
                                <div class="file-upload-container" onclick="document.getElementById('fileInput').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="file-upload-text">Click or drag and drop file here</p>
                                    <p class="file-upload-text">(Supported format: .csv and .xlsx)</p>
                                    <div class="file-name"></div>
                                    <input type="file" 
                                        id="fileInput" 
                                        accept=".xlsx,.xls,.csv" 
                                        onchange="updateFileName(this)" 
                                        style="display: none;">
                                </div>
                                <div class="template-downloads">
                                    <button type="button" class="btn btn-template excel" onclick="downloadExcelTemplate(event)">
                                        <i class="fas fa-file-download"></i> Download Template
                                    </button>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Import Students</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>`;

            document.body.insertAdjacentHTML('beforeend', modal);
            $('#importStudentModal').modal('show');
            $('#importStudentModal').on('hidden.bs.modal', function() {
                this.remove();
            });
        }

        function updateFileName(input) {
            const fileName = input.files[0]?.name;
            const fileNameDiv = document.querySelector('.file-name');
            if (fileName) {
                fileNameDiv.textContent = `Selected file: ${fileName}`;
                fileNameDiv.style.display = 'block';
            } else {
                fileNameDiv.style.display = 'none';
            }
        }

        function downloadExcelTemplate(event) {
            event.preventDefault();
            
            // Define the headers and sample data
            const headers = ['LRN', 'First Name', 'Middle Name', 'Last Name', 'Email', 'Contact Number', 'Gender'];
            const sampleData = [
                ['123456789012', 'Juan', 'Santos', 'Dela Cruz', 'juan.delacruz@example.com', '09123456789', 'Male'],
                ['123456789013', 'Maria', 'Garcia', 'Santos', 'maria.santos@example.com', '09987654321', 'Female']
            ];

            // Create workbook and worksheet
            const wb = XLSX.utils.book_new();
            const ws_data = [headers, ...sampleData];
            const ws = XLSX.utils.aoa_to_sheet(ws_data);

            // Set column widths
            const colWidths = [
                {wch: 15},  // LRN
                {wch: 15},  // First Name
                {wch: 15},  // Middle Name
                {wch: 15},  // Last Name
                {wch: 30},  // Email
                {wch: 15},  // Contact Number
                {wch: 10}   // Gender
            ];
            ws['!cols'] = colWidths;

            // Format all columns as text
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let R = range.s.r; R <= range.e.r + 1000; R++) { // Extended to 1000 more rows
                for (let C = 0; C <= 6; C++) { // Format all columns (A through G)
                    const cellRef = XLSX.utils.encode_cell({r: R, c: C});
                    if (!ws[cellRef]) {
                        ws[cellRef] = { t: 's', v: '' };
                    }
                    ws[cellRef].z = '@';
                    ws[cellRef].t = 's';
                    
                    // Convert any existing number values to strings
                    if (typeof ws[cellRef].v === 'number') {
                        ws[cellRef].v = String(ws[cellRef].v);
                    }
                }
            }

            // Add data validation for Gender column (column G)
            ws['!dataValidation'] = {
                G2: {
                    type: 'list',
                    operator: 'equal',
                    formula1: '"Male,Female"',
                    showDropDown: true,
                    allowBlank: false
                }
            };

            // Set the worksheet range to include all formatted cells
            ws['!ref'] = XLSX.utils.encode_range({
                s: {r: 0, c: 0},
                e: {r: 1000, c: 6}
            });

            // Add the worksheet to the workbook
            XLSX.utils.book_append_sheet(wb, ws, "Template");

            // Generate the file
            XLSX.writeFile(wb, "student_import_template.xlsx");
        }

        function handleImportStudents(event) {
            event.preventDefault();
            
            const fileInput = document.getElementById('fileInput');
            const file = fileInput.files[0];
            
            if (!file) {
                Swal.fire('Error', 'Please select a file to import', 'error');
                return;
            }

            // Create FormData and append file
            const formData = new FormData();
            formData.append('file', file);
            formData.append('action', 'import_students');

            // Show loading state
            Swal.fire({
                title: 'Importing...',
                text: 'Please wait while we process your file',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: 'handlers/student_handler.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        if (result.status === 'success') {
                            Swal.fire({
                                title: 'Success',
                                html: `Successfully imported students<br>
                                      Added: ${result.added}<br>
                                      Skipped: ${result.skipped}<br>
                                      ${result.errors.length ? '<br>Errors:<br>' + result.errors.join('<br>') : ''}`,
                                icon: 'success'
                            }).then(() => {
                                $('#importStudentModal').modal('hide');
                                $('#studentsTable').DataTable().ajax.reload();
                                fileInput.value = '';
                            });
                        } else {
                            Swal.fire('Error', result.message || 'Import failed', 'error');
                        }
                    } catch (e) {
                        console.error('Parse error:', e);
                        Swal.fire('Error', 'Invalid server response', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Import error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    Swal.fire('Error', 'Failed to import students. Please check the file format and try again.', 'error');
                }
            });
        }
    </script>
</body>
</html>
