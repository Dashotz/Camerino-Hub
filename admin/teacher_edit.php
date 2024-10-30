<?php include 'header.php'; ?>


<link rel="stylesheet" href="teacher_edit.css">

<main class="container my-5">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h2 class="card-title mb-4">Edit an account</h2>
                    <p class="text-muted">Fill out wisely!</p>
                    <form>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="firstName" class="form-label">First name</label>
                                <input type="text" class="form-control" id="firstName" value="Christine">
                            </div>
                            <div class="col">
                                <label for="lastName" class="form-label">Last name</label>
                                <input type="text" class="form-control" id="lastName" value="Hermosa">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="emailAddress" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="emailAddress" value="christinyr56">
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" value="123456789">
                                <div class="form-text">Use 8 or more characters with a mix of letters, numbers & symbols</div>
                            </div>
                            <div class="col">
                                <label for="department" class="form-label">Confirm the Department</label>
                                <select class="form-select" id="department">
                                    <option selected>Select Department</option>
                                    <!-- Add department options here -->
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="showPassword">
                            <label class="form-check-label" for="showPassword">Show password</label>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <div class="custom-file-input">
                                <input type="file" class="form-control" id="image" hidden>
                                <label for="image" class="file-label">
                                    <span class="file-name">Choose File</span>
                                    <span class="file-button">Browse</span>
                                </label>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="#" class="btn btn-link">< Back</a>
                            <button type="submit" class="btn btn-primary float-end">Update</button>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 d-flex align-items-center justify-content-center">
                    <img src="../images/Illustration.png" alt="Geometric shape" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Language and links section -->
    <div class="row mt-5">
        <div class="col-md-6">
            <select class="form-select w-auto">
                <option selected>English (United States)</option>
                <!-- Add more language options here -->
            </select>
        </div>
        <div class="col-md-6 text-end">
            <a href="#" class="text-muted me-3">Help</a>
            <a href="#" class="text-muted me-3">Privacy</a>
            <a href="#" class="text-muted">Terms</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>


<script>
document.getElementById('image').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    var fileLabel = this.nextElementSibling.querySelector('.file-name');
    fileLabel.textContent = fileName;
});
</script>
