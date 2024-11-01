<?php include 'header.php'; ?>

<main class="container my-5">
    <!-- Search and Filter Section -->
    <section class="search-filter-container mb-4">
        <div class="search-bar">
            <input type="text" placeholder="Search Student" class="form-control">
            <button class="btn btn-primary search-btn">Search</button>
        </div>
        <div class="filters">
            <select class="form-control">
                <option>Grade Level</option>
            </select>
            <select class="form-control">
                <option>Section</option>
            </select>
            <select class="form-control">
                <option>Sort By: Name</option>
            </select>
        </div>
    </section>

    <!-- Student Table Section -->
    <section class="student-table">
        <div class="table-header">
            <h2>Student Table <span class="text-muted">records for page</span></h2>
            <div class="table-actions">
                <button class="btn btn-light"><i class="fas fa-sort-amount-down"></i> 10</button>
                <button class="btn btn-light"><i class="fas fa-trash"></i> Delete</button>
                <button class="btn btn-light"><i class="fas fa-filter"></i> Filters</button>
                <button class="btn btn-light"><i class="fas fa-file-export"></i> Export</button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox"></th>
                        <th>Photo</th>
                        <th>Username <i class="fas fa-sort"></i></th>
                        <th>Password <i class="fas fa-sort"></i></th>
                        <th>Name <i class="fas fa-sort"></i></th>
                        <th>Grade & Section <i class="fas fa-sort"></i></th>
                        <th>Actions <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/student1.jpg" alt="Maria Garcia" class="student-photo"></td>
                        <td>maria_garcia2010</td>
                        <td>student123</td>
                        <td>Maria Garcia</td>
                        <td>Grade 7 - Sampaguita</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/student2.jpg" alt="John Santos" class="student-photo"></td>
                        <td>john_santos2011</td>
                        <td>pass4321</td>
                        <td>John Santos</td>
                        <td>Grade 8 - Orchid</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/student3.jpg" alt="Sarah Lee" class="student-photo"></td>
                        <td>sarah_lee2009</td>
                        <td>lee5678</td>
                        <td>Sarah Lee</td>
                        <td>Grade 9 - Rose</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/student4.jpg" alt="Michael Reyes" class="student-photo"></td>
                        <td>michael_reyes2010</td>
                        <td>reyes9876</td>
                        <td>Michael Reyes</td>
                        <td>Grade 7 - Sunflower</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <p>Showing 1 to 4 of 4 entries</p>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-angle-double-left"></i></a></li>
                <li class="page-item disabled"><a class="page-link" href="#"><i class="fas fa-angle-left"></i></a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">...</a></li>
                <li class="page-item"><a class="page-link" href="#">10</a></li>
                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-angle-right"></i></a></li>
                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-angle-double-right"></i></a></li>
            </ul>
        </nav>
    </section>
</main>

<?php include 'footer.php'; ?>

