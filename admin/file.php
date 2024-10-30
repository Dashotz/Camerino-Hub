<?php include 'header.php'; ?>

<main class="container my-5">
    <!-- Search and Filter Section -->
    <section class="search-filter-container mb-4">
        <div class="search-bar">
            <input type="text" placeholder="Search Something" class="form-control">
            <button class="btn btn-primary search-btn">Search</button>
        </div>
        <div class="filters">
            <select class="form-control">
                <option>Availability</option>
            </select>
            <select class="form-control">
                <option>Filter</option>
            </select>
            <select class="form-control">
                <option>Sort By: Relevance</option>
            </select>
        </div>
    </section>

    <!-- Material Table Section -->
    <section class="material-table">
        <div class="table-header">
            <h2>Material Table <span class="text-muted">records for page</span></h2>
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
                        <th>File Name <i class="fas fa-sort"></i></th>
                        <th>Description <i class="fas fa-sort"></i></th>
                        <th>Date Upload <i class="fas fa-sort"></i></th>
                        <th>Uploaded By <i class="fas fa-sort"></i></th>
                        <th>Class <i class="fas fa-sort"></i></th>
                        <th>Actions <i class="fas fa-sort"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="file-icon">CHAPTER 1 - MATH</span></td>
                        <td>This chapter covers number types and basic operations, along with key properties like commutative and associative laws.</td>
                        <td>2024-03-24 13:40:58</td>
                        <td>John Michael Reyes</td>
                        <td>7 - Santan</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="file-icon">CHAPTER 1 - SCIENCE</span></td>
                        <td>This chapter introduces basic scientific concepts, the scientific method, and fundamental principles of physics and chemistry.</td>
                        <td>2024-03-25 09:15:30</td>
                        <td>Maria Santos</td>
                        <td>7 - Rose</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="file-icon">CHAPTER 1 - ENGLISH</span></td>
                        <td>This chapter focuses on grammar basics, parts of speech, and sentence structure to improve writing and communication skills.</td>
                        <td>2024-03-26 11:20:45</td>
                        <td>Robert Johnson</td>
                        <td>7 - Sampaguita</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="file-icon">CHAPTER 1 - HISTORY</span></td>
                        <td>This chapter covers early civilizations, ancient empires, and the foundations of world history.</td>
                        <td>2024-03-27 14:05:22</td>
                        <td>Elena Cruz</td>
                        <td>7 - Orchid</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="file-icon">CHAPTER 1 - PHYSICAL EDUCATION</span></td>
                        <td>This chapter introduces basic fitness concepts, team sports, and the importance of physical activity for overall health.</td>
                        <td>2024-03-28 10:30:15</td>
                        <td>Carlos Reyes</td>
                        <td>7 - Sunflower</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><span class="file-icon">CHAPTER 1 - MUSIC</span></td>
                        <td>This chapter covers basic music theory, note reading, and an introduction to various musical instruments and styles.</td>
                        <td>2024-03-29 13:45:50</td>
                        <td>Sofia Garcia</td>
                        <td>7 - Jasmine</td>
                        <td>
                            <button class="btn btn-sm btn-light"><i class="fas fa-trash"></i> Delete</button>
                            <button class="btn btn-sm btn-light"><i class="fas fa-ellipsis-v"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-angle-double-left"></i></a></li>
                <li class="page-item"><a class="page-link" href="#"><i class="fas fa-angle-left"></i></a></li>
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
