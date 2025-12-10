# Sound Buttons WordPress Plugin

A comprehensive WordPress plugin for creating a sound board website with MP3 sound management, categories, favorites, sharing, and secure downloads. Perfect for gamers and content creators.

## Features

### Core Functionality

- **Custom Post Type**: Dedicated "Sounds" post type for managing audio files
- **Hierarchical Categories**: Organize sounds with custom taxonomy support
- **MP3 Support**: Secure upload and playback of MP3 audio files
- **Statistics Tracking**: Track play counts and favorite counts
- **Favorites System**: Users can favorite sounds (works for logged-in users and guests via IP)
- **Secure Downloads**: Download protection with nonce verification and rate limiting
- **Share Functionality**: Share sounds via social media or direct link

### Admin Features

- **Single Upload**: Upload individual sound files with metadata
- **Bulk Upload**: Upload multiple MP3 files simultaneously
- **Category Management**: Create and manage sound categories with custom colors
- **Statistics Dashboard**: View play counts, favorites, and downloads
- **Settings Page**: Configure download limits, sounds per page, and features

### Frontend Features

- **10 Unique Button Designs**: Vibrant, gaming-inspired button variants with gradients
- **Responsive Grid Layout**: Mobile-first design that works on all devices
- **Individual Sound Pages**: Dedicated pages for each sound with related sounds
- **Category Archives**: Browse sounds by category
- **Breadcrumb Navigation**: Easy navigation throughout the site
- **Audio Player**: Smooth play/pause functionality with visual feedback
- **Action Buttons**: Favorite, share, and download with one click

### Design

- **Gaming Aesthetic**: High-energy, vibrant colors and smooth animations
- **10 Button Variants**: Unique gradient designs that rotate across sounds
  1. Electric Blue
  2. Neon Pink
  3. Cyber Green
  4. Fire Orange
  5. Purple Haze
  6. Ocean Blue
  7. Sunset Red
  8. Mint Fresh
  9. Royal Purple
  10. Lava Red
- **Smooth Animations**: 300ms transitions with cubic-bezier easing
- **Pulse Effects**: Playing buttons animate with a pulse effect
- **Hover Effects**: Glow, scale, and color shifts on interaction

## Installation

1. **Upload the Plugin**
   ```
   Upload the entire `sound-buttons-plugin` folder to `/wp-content/plugins/`
   ```

2. **Activate the Plugin**
   ```
   Go to WordPress Admin → Plugins → Activate "Sound Buttons Plugin"
   ```

3. **Plugin Initialization**
   - Upon activation, the plugin will:
     - Create custom post type "Sounds"
     - Create taxonomy "Sound Categories"
     - Create database tables for statistics and favorites
     - Set default plugin options

## Configuration

### Settings

Navigate to **Sound Buttons → Settings** to configure:

- **Sounds Per Page**: Number of sounds to display on archive pages (default: 30)
- **Download Protection**: Enable/disable download security (default: enabled)
- **Download Rate Limit**: Maximum downloads per IP per hour (default: 10)
- **Statistics Tracking**: Enable/disable play count tracking (default: enabled)
- **Favorites System**: Enable/disable favorites functionality (default: enabled)

## Usage

### Uploading Sounds

#### Single Upload
1. Go to **Sound Buttons → Upload Sounds**
2. Select the "Single Upload" tab
3. Choose an MP3 file
4. Enter sound title and description
5. Select one or more categories
6. Click "Upload Sound"

#### Bulk Upload
1. Go to **Sound Buttons → Upload Sounds**
2. Select the "Bulk Upload" tab
3. Select multiple MP3 files
4. Enter a default description (optional)
5. Select categories to apply to all sounds
6. Click "Upload Sounds"
7. File names will be automatically converted to titles

### Managing Categories

1. Go to **Sound Buttons → Categories**
2. Create categories with:
   - Name and slug
   - Description
   - Parent category (for hierarchical structure)
   - Custom color (for visual styling)

### Editing Sounds

1. Go to **Sound Buttons → All Sounds**
2. Click on a sound to edit
3. Modify:
   - Title and description
   - Audio file (by attachment ID)
   - Categories
   - Button variant (1-10)
4. View statistics:
   - Total plays
   - Total favorites
   - Total downloads

### Displaying Sounds

#### Using Shortcodes

**Display Sounds Grid:**
```php
[sound_buttons limit="30"]
```

**Display Sounds by Category:**
```php
[sound_buttons category="game-sounds" limit="12"]
```

**Display Trending Sounds:**
```php
[trending_sounds limit="10" days="7"]
```

#### Using Templates

The plugin includes custom templates that automatically load:

- **Single Sound Page**: `single-sound.php`
- **Category Archive**: `archive-sound-category.php`
- **Sound Archive**: `archive-sound.php`

To customize templates, copy them to your theme:
```
/wp-content/themes/your-theme/sound-buttons/single-sound.php
```

### Accessing Sound Pages

- **All Sounds**: `yoursite.com/sound/`
- **Single Sound**: `yoursite.com/sound/sound-name/`
- **Category**: `yoursite.com/sound-category/category-name/`

## File Structure

```
sound-buttons-plugin/
├── sound-buttons-plugin.php   # Main plugin file
├── README.md                   # Documentation
├── includes/                   # Core functionality
│   ├── class-sbp-post-types.php
│   ├── class-sbp-taxonomies.php
│   ├── class-sbp-upload-handler.php
│   ├── class-sbp-statistics.php
│   ├── class-sbp-favorites.php
│   ├── class-sbp-download-handler.php
│   └── class-sbp-template-loader.php
├── admin/                      # Admin functionality
│   ├── class-sbp-admin.php
│   ├── class-sbp-admin-upload.php
│   ├── class-sbp-admin-categories.php
│   ├── css/
│   │   └── admin-style.css
│   └── js/
│       └── admin-script.js
├── public/                     # Frontend functionality
│   ├── class-sbp-public.php
│   ├── css/
│   │   └── style.css          # All 10 button variants
│   └── js/
│       └── script.js          # Audio player & interactions
└── templates/                  # Template files
    ├── content-sound-card.php
    ├── single-sound.php
    ├── archive-sound.php
    └── archive-sound-category.php
```

## Database Tables

The plugin creates two custom tables:

### `wp_sbp_statistics`
Tracks sound plays
- `id`: Unique play ID
- `sound_id`: Sound post ID
- `user_ip`: User IP address
- `played_at`: Timestamp

### `wp_sbp_favorites`
Tracks favorites
- `id`: Unique favorite ID
- `sound_id`: Sound post ID
- `user_id`: WordPress user ID (if logged in)
- `user_ip`: User IP address (for guests)
- `created_at`: Timestamp

## Security Features

### Download Protection
- **Nonce Verification**: Each download link includes a unique nonce
- **Rate Limiting**: Limits downloads per IP address (configurable)
- **User Agent Validation**: Blocks common bots and scrapers
- **Download Tracking**: Logs all downloads for analytics

### Data Sanitization
- All user inputs are sanitized using WordPress functions
- SQL queries use prepared statements
- Output is properly escaped

## Performance Optimization

- **Lazy Loading**: Sound buttons load efficiently
- **Minimal HTTP Requests**: Combined CSS/JS files
- **Optimized Queries**: Efficient database queries with proper indexing
- **CDN-Ready**: Asset URLs work with CDN integration
- **Minification-Ready**: Code structure supports minification

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## Hooks & Filters

### Actions
```php
// After sound upload
do_action('sbp_after_sound_upload', $post_id, $file_data);

// After favorite toggle
do_action('sbp_after_favorite_toggle', $sound_id, $action);
```

### Filters
```php
// Modify sounds per page
apply_filters('sbp_sounds_per_page', 30);

// Modify download URL
apply_filters('sbp_download_url', $url, $sound_id);

// Modify button variant
apply_filters('sbp_button_variant', $variant, $sound_id);
```

## Troubleshooting

### Sounds Not Playing
- Check that the MP3 file exists in the media library
- Verify the audio URL is accessible
- Check browser console for errors
- Ensure the file isn't blocked by server security

### Upload Errors
- Check PHP upload limits (`upload_max_filesize`, `post_max_size`)
- Verify file permissions on `/wp-content/uploads/`
- Ensure MP3 MIME type is allowed in WordPress

### Download Protection Issues
- Clear browser cache
- Check if downloads are being rate limited
- Verify nonce generation is working
- Test with download protection disabled

### Permalinks Not Working
- Go to **Settings → Permalinks**
- Click "Save Changes" to flush rewrite rules
- Ensure `.htaccess` is writable

## Development

### Customizing Button Variants

Edit `public/css/style.css` and modify the variant classes:

```css
.sbp-variant-1 {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}
```

### Adding Custom Fields

Use WordPress hooks in your theme:

```php
add_action('sbp_after_sound_content', function($post_id) {
    // Your custom content
});
```

## Future Enhancements (Phase 2)

- User login/signup system
- Private sound editor for logged-in users
- User sound upload functionality
- Personal soundboards/playlists
- Sound waveform visualization
- Advanced search and filtering
- API integration for mobile apps

## Support

For issues, feature requests, or questions:
- GitHub Issues: [Create an issue]
- Documentation: See this README
- WordPress Support: Check WordPress.org forums

## Credits

- **Developed by**: Sound Buttons Team
- **Icons**: SVG icons included
- **Fonts**: System fonts for performance

## License

GPL v2 or later

## Changelog

### Version 1.0.0 (Initial Release)
- Custom post type and taxonomy
- Single and bulk upload
- 10 unique button variants
- Statistics tracking
- Favorites system
- Secure downloads
- Share functionality
- Responsive design
- Admin interface
- Settings page

---

**Happy Sound Boarding!** 🎵🎮
