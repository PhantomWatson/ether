var search = {
    init: function () {
        $('#header-search input').on('input', function () {
            // Remove spaces
            var input = $(this);
            var original = input.val();
            var spaceless = original.replace(' ', '');
            if (original !== spaceless) {
                input.val(spaceless);
            }

            search.filterCloud(original);
        });
    },
    filterCloud: function (searchTerm) {
        var cloud = $('#frontpage_cloud');

        // Skip if not on front page
        if (cloud.length === 0) {
            return;
        }

        // Show all words if search term is empty
        if (searchTerm === '') {
            cloud.find('a').show();
            return;
        }

        cloud.find('> a.thoughtword').each(function () {
            var link = $(this);
            var word = $(link).html().trim();
            if (word.search(searchTerm) === -1) {
                link.hide();
            } else {
                link.show();
            }
        });
    }
};
