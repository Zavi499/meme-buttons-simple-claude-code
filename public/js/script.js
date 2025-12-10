/**
 * Sound Buttons Plugin - Public JavaScript
 */

(function($) {
    'use strict';

    // Global audio player
    let currentAudio = null;
    let currentButton = null;

    /**
     * Sound Button Player
     */
    class SoundButtonPlayer {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            this.initShareModal();
        }

        bindEvents() {
            // Play button clicks
            $(document).on('click', '.sbp-play-button', this.handlePlayClick.bind(this));

            // Favorite button clicks
            $(document).on('click', '.sbp-favorite-btn, .sbp-favorite-btn-large', this.handleFavoriteClick.bind(this));

            // Share button clicks
            $(document).on('click', '.sbp-share-btn', this.handleShareClick.bind(this));

            // Share modal close
            $(document).on('click', '.sbp-share-modal-close, .sbp-share-modal-overlay', this.closeShareModal.bind(this));

            // Copy link button
            $(document).on('click', '.sbp-copy-link-btn', this.handleCopyLink.bind(this));

            // Prevent modal close when clicking inside
            $(document).on('click', '.sbp-share-modal-content', function(e) {
                e.stopPropagation();
            });

            // ESC key to close modal
            $(document).on('keydown', this.handleKeyDown.bind(this));
        }

        /**
         * Handle play button click
         */
        handlePlayClick(e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const audioUrl = $button.data('audio-url');
            const soundId = $button.data('sound-id');

            // If clicking the same button that's playing, pause it
            if (currentButton && currentButton[0] === $button[0] && currentAudio && !currentAudio.paused) {
                this.pauseAudio();
                return;
            }

            // Stop any currently playing audio
            if (currentAudio) {
                this.stopAudio();
            }

            // Create and play new audio
            this.playAudio($button, audioUrl, soundId);
        }

        /**
         * Play audio
         */
        playAudio($button, audioUrl, soundId) {
            currentAudio = new Audio(audioUrl);
            currentButton = $button;

            // Update button state
            this.updateButtonState($button, 'playing');

            // Play audio
            currentAudio.play().catch((error) => {
                console.error('Audio play failed:', error);
                this.updateButtonState($button, 'paused');
            });

            // Track play count
            this.trackPlay(soundId);

            // Handle audio end
            currentAudio.addEventListener('ended', () => {
                this.updateButtonState($button, 'paused');
                currentAudio = null;
                currentButton = null;
            });

            // Handle errors
            currentAudio.addEventListener('error', () => {
                console.error('Audio loading failed');
                this.updateButtonState($button, 'paused');
                currentAudio = null;
                currentButton = null;
            });
        }

        /**
         * Pause audio
         */
        pauseAudio() {
            if (currentAudio) {
                currentAudio.pause();
                if (currentButton) {
                    this.updateButtonState(currentButton, 'paused');
                }
            }
        }

        /**
         * Stop audio
         */
        stopAudio() {
            if (currentAudio) {
                currentAudio.pause();
                currentAudio.currentTime = 0;
                if (currentButton) {
                    this.updateButtonState(currentButton, 'paused');
                }
                currentAudio = null;
                currentButton = null;
            }
        }

        /**
         * Update button state
         */
        updateButtonState($button, state) {
            const $playIcon = $button.find('.sbp-play-icon');
            const $pauseIcon = $button.find('.sbp-pause-icon');

            if (state === 'playing') {
                $button.addClass('is-playing');
                $playIcon.hide();
                $pauseIcon.show();
            } else {
                $button.removeClass('is-playing');
                $playIcon.show();
                $pauseIcon.hide();
            }
        }

        /**
         * Track play count
         */
        trackPlay(soundId) {
            $.ajax({
                url: sbpData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'sbp_track_play',
                    sound_id: soundId,
                    nonce: sbpData.nonce
                },
                success: function(response) {
                    if (response.success && response.data.play_count) {
                        // Update play count display if exists
                        $('.sbp-play-count-' + soundId).text(response.data.play_count);
                    }
                }
            });
        }

        /**
         * Handle favorite button click
         */
        handleFavoriteClick(e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const soundId = $button.data('sound-id');

            // Disable button during request
            $button.prop('disabled', true);

            $.ajax({
                url: sbpData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'sbp_toggle_favorite',
                    sound_id: soundId,
                    nonce: sbpData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        const data = response.data;

                        // Update button state
                        if (data.is_favorited) {
                            $button.addClass('is-favorited');
                        } else {
                            $button.removeClass('is-favorited');
                        }

                        // Update count
                        $button.find('.sbp-favorite-count').text(data.favorite_count);

                        // Show feedback
                        this.showFeedback($button, data.action === 'added' ? 'Added to favorites!' : 'Removed from favorites');
                    }
                },
                error: () => {
                    this.showFeedback($button, 'Failed to update favorite', 'error');
                },
                complete: () => {
                    $button.prop('disabled', false);
                }
            });
        }

        /**
         * Handle share button click
         */
        handleShareClick(e) {
            e.preventDefault();

            const $button = $(e.currentTarget);
            const soundTitle = $button.data('sound-title');
            const soundUrl = $button.data('sound-url');

            this.openShareModal(soundTitle, soundUrl);
        }

        /**
         * Initialize share modal
         */
        initShareModal() {
            if ($('#sbp-share-modal').length === 0) {
                const modalHtml = `
                    <div id="sbp-share-modal" class="sbp-share-modal" style="display: none;">
                        <div class="sbp-share-modal-overlay"></div>
                        <div class="sbp-share-modal-content">
                            <button class="sbp-share-modal-close">&times;</button>
                            <h3 class="sbp-share-modal-title">Share Sound</h3>
                            <p class="sbp-share-sound-title"></p>
                            <div class="sbp-share-options">
                                <div class="sbp-share-link-group">
                                    <input type="text" class="sbp-share-link-input" readonly>
                                    <button class="sbp-copy-link-btn">Copy Link</button>
                                </div>
                                <div class="sbp-share-social">
                                    <a href="#" class="sbp-share-social-btn sbp-share-twitter" target="_blank" rel="noopener">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>
                                        </svg>
                                        Twitter
                                    </a>
                                    <a href="#" class="sbp-share-social-btn sbp-share-facebook" target="_blank" rel="noopener">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>
                                        </svg>
                                        Facebook
                                    </a>
                                    <a href="#" class="sbp-share-social-btn sbp-share-reddit" target="_blank" rel="noopener">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/>
                                        </svg>
                                        Reddit
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('body').append(modalHtml);
            }
        }

        /**
         * Open share modal
         */
        openShareModal(title, url) {
            const $modal = $('#sbp-share-modal');
            const encodedTitle = encodeURIComponent(title);
            const encodedUrl = encodeURIComponent(url);

            // Set content
            $modal.find('.sbp-share-sound-title').text(title);
            $modal.find('.sbp-share-link-input').val(url);

            // Set social links
            $modal.find('.sbp-share-twitter').attr('href', `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`);
            $modal.find('.sbp-share-facebook').attr('href', `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`);
            $modal.find('.sbp-share-reddit').attr('href', `https://reddit.com/submit?url=${encodedUrl}&title=${encodedTitle}`);

            // Show modal
            $modal.fadeIn(200);
            $('body').addClass('sbp-modal-open');
        }

        /**
         * Close share modal
         */
        closeShareModal(e) {
            if (e) {
                e.preventDefault();
            }

            const $modal = $('#sbp-share-modal');
            $modal.fadeOut(200);
            $('body').removeClass('sbp-modal-open');
        }

        /**
         * Handle copy link
         */
        handleCopyLink(e) {
            e.preventDefault();

            const $input = $('.sbp-share-link-input');
            $input[0].select();
            $input[0].setSelectionRange(0, 99999); // For mobile

            try {
                document.execCommand('copy');
                this.showFeedback($(e.currentTarget), 'Link copied!');
            } catch (err) {
                console.error('Copy failed:', err);
                this.showFeedback($(e.currentTarget), 'Failed to copy', 'error');
            }
        }

        /**
         * Handle keyboard events
         */
        handleKeyDown(e) {
            // ESC key
            if (e.keyCode === 27) {
                this.closeShareModal();
            }
        }

        /**
         * Show feedback message
         */
        showFeedback($element, message, type = 'success') {
            const $feedback = $('<div class="sbp-feedback sbp-feedback-' + type + '">' + message + '</div>');
            $element.append($feedback);

            setTimeout(() => {
                $feedback.fadeOut(200, function() {
                    $(this).remove();
                });
            }, 2000);
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        new SoundButtonPlayer();
    });

})(jQuery);
