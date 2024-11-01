<?php include 'header.php'; ?>

<main class="container my-5">
    <!-- Search and Filter Section -->
    <section class="search-filter-container mb-4">
        <div class="search-bar">
            <input type="text" placeholder="Search Teacher" class="form-control">
            <button class="btn btn-primary search-btn">Search</button>
        </div>
        <div class="filters">
            <select class="form-control">
                <option>Department</option>
            </select>
            <select class="form-control">
                <option>Status</option>
            </select>
            <select class="form-control">
                <option>Sort By: Name</option>
            </select>
        </div>
    </section>

    <!-- Teacher Table Section -->
    <section class="teacher-table">
        <div class="table-header">
            <h2>Teacher Table <span class="text-muted">records for page</span></h2>
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
                        <th>Department <i class="fas fa-sort"></i></th>
                        <th>Actions <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/image1.jpg" alt="Christine Reyes" class="teacher-photo"></td>
                        <td>christinyr56</td>
                        <td>123456789</td>
                        <td>Christine Reyes</td>
                        <td>Math and MAPEH Department</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/image2.jpg" alt="Jonathan Lui" class="teacher-photo"></td>
                        <td>Jonathan_Lui</td>
                        <td>234567890</td>
                        <td>Jonathan Lui</td>
                        <td>English Department</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/image3.jpg" alt="Alexander Mendez" class="teacher-photo"></td>
                        <td>AlexandrJ_XD</td>
                        <td>345678901</td>
                        <td>Alexander Mendez</td>
                        <td>MAPEH Department</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-edit"></i> Edit</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><img src="path/to/image4.jpg" alt="Faye Decker" class="teacher-photo"></td>
                        <td>Decker_Faye3</td>
                        <td>456789012</td>
                        <td>Faye Decker</td>
                        <td>Science Department</td>
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
