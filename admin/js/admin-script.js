/**
 * Sound Buttons Plugin - Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Tab switching for upload page
         */
        $('.nav-tab').on('click', function(e) {
            e.preventDefault();

            var target = $(this).attr('href');

            // Update tabs
            $('.nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            // Update content
            $('.sbp-tab-content').hide();
            $(target).show();
        });

        /**
         * File input preview
         */
        $('input[type="file"]').on('change', function() {
            var fileCount = this.files.length;
            var $label = $(this).next('label');

            if (fileCount > 0) {
                if (fileCount === 1) {
                    $label.text(this.files[0].name);
                } else {
                    $label.text(fileCount + ' files selected');
                }
            }
        });

        /**
         * Confirm before deleting sounds
         */
        $('.submitdelete').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this sound? This action cannot be undone.')) {
                e.preventDefault();
                return false;
            }
        });

        /**
         * Add WordPress color picker if available
         */
        if ($.fn.wpColorPicker) {
            $('.sbp-color-picker').wpColorPicker();
        }

        /**
         * Auto-fill title from filename on single upload
         */
        $('#sound_file').on('change', function() {
            var filename = this.files[0].name;
            var title = filename.replace(/\.[^/.]+$/, ""); // Remove extension
            title = title.replace(/[-_]/g, ' '); // Replace hyphens and underscores with spaces
            title = title.replace(/\b\w/g, function(l) { return l.toUpperCase(); }); // Capitalize words

            var $titleField = $('#sound_title');
            if ($titleField.val() === '') {
                $titleField.val(title);
            }
        });

        /**
         * Bulk upload file count display
         */
        $('#bulk_files').on('change', function() {
            var fileCount = this.files.length;
            var $info = $('#bulk-file-info');

            if ($info.length === 0) {
                $info = $('<p id="bulk-file-info" class="description"></p>');
                $(this).after($info);
            }

            if (fileCount > 0) {
                $info.text(fileCount + ' file(s) selected for upload');
            } else {
                $info.text('');
            }
        });

        /**
         * Validate MP3 files
         */
        $('input[type="file"][accept*="mp3"]').on('change', function() {
            var files = this.files;
            var invalidFiles = [];

            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file.type !== 'audio/mpeg' && !file.name.toLowerCase().endsWith('.mp3')) {
                    invalidFiles.push(file.name);
                }
            }

            if (invalidFiles.length > 0) {
                alert('The following files are not valid MP3 files:\n' + invalidFiles.join('\n'));
                this.value = '';
                return false;
            }
        });

        /**
         * AJAX form submission for settings (optional enhancement)
         */
        $('#sbp-settings-form').on('submit', function(e) {
            // Add loading state
            var $submitBtn = $(this).find('button[type="submit"]');
            var originalText = $submitBtn.text();

            $submitBtn.prop('disabled', true).text('Saving...');

            // Re-enable after form processes (WordPress handles the actual submission)
            setTimeout(function() {
                $submitBtn.prop('disabled', false).text(originalText);
            }, 2000);
        });

    });

})(jQuery);
