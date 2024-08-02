$(document).ready(function() {


    
    function displayCategories(categories) {
        const categoryDiv = $(".navbar-nav");
        categoryDiv.empty(); 

        categories.forEach((category) => {
            const dropdownItem = $(`
                <li class="nav-item dropdown ">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown${category.Category_ID}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        ${category.Name}
                    </a>
                    <div class="dropdown-menu drpdown" aria-labelledby="navbarDropdown${category.Category_ID}">
                    </div>
                </li>
            `);
            categoryDiv.append(dropdownItem);
        });

        handleDropdownBehavior();
    }

    function getSubCategories(dropdown, categoryId) {
        const dropdownMenu = $(dropdown).find('.drpdown');
        
        if (dropdownMenu.children().length === 0) {
            $.ajax({
                type: "GET",
                url: 'handle.php',
                data: { action: 'getSubcategories', id: categoryId },
                success: function(response) {
                    fetchSubCategories(response, dropdownMenu);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    }

    function fetchSubCategories(response, dropdownMenu) {
        dropdownMenu.empty().addClass('multi-column');
        const columnCount = Math.min(3, Math.ceil(response.length / 7));
        
        for (let i = 0; i < columnCount; i++) {
            dropdownMenu.append($('<div class="subcategory-column"></div>'));
        }

        response.forEach((subCategory, index) => {
            const columnIndex = index % columnCount;
            const subCategoryItem = $(`
                <a class="dropdown-item" href="#" data-subcategory-id="${subCategory.Sub_ID}">
                    ${subCategory.Name}
                </a>
            `);
            dropdownMenu.find('.subcategory-column').eq(columnIndex).append(subCategoryItem);
        });
    }

    function handleDropdownBehavior() {
        const isLargeScreen = window.innerWidth >= 992;
    
        $('.nav-item.dropdown').each(function() {
            const $this = $(this);
            const categoryId = $this.find('.dropdown-toggle').attr('id').replace('navbarDropdown', '');
            const $dropdownMenu = $this.find('.drpdown');
            const $dropdownToggle = $this.find('.dropdown-toggle');
    
            $this.off('mouseenter mouseleave click');
            $dropdownToggle.off('click');
    
            if (isLargeScreen) {
                $this.hover(
                    function() { 
                        getSubCategories(this, categoryId);
                        $dropdownMenu.stop(true, true).show();
                    },
                    function() { 
                        $dropdownMenu.stop(true, true).hide();
                    }
                );
            } 
            else {
                $dropdownToggle.on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    
                    $(this).prop('disabled', true);
    
                    $('.drpdown').not($dropdownMenu).hide();
                    
                    $dropdownMenu.toggle();
                    if ($dropdownMenu.is(':visible')) {
                        getSubCategories($this[0], categoryId);
                    }
    
                   
                  
                });
            }
        });
    
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.nav-item.dropdown').length) {
                $('.drpdown').hide();
                $(this).prop('disabled', false);    
            }
        });
    }

    $(document).on('click', '.dropdown-item[data-subcategory-id]', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const subCategoryId = $(this).data('subcategory-id');
        window.location.href = `items.php?sub_id=${subCategoryId}`;
    });
    

    $(window).on('resize scroll', handleDropdownBehavior);

    displayCategories(categories);
    
});
