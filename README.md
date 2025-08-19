# BB Color Presets First

> **Why we released this:** We got tired of constantly clicking the BB color presets tab when building sites at Weave Digital Studio and HumanKind Funeral Websites. We wanted to set our client's brand indentity colours once at the beginning (using Generate Press Global Colours or BB Global colours) and access these quickly in Beaver Builder. **Plus,** it helps prevent clients from accidentally adding non-brand colors to their sites. This tiny plugin saves us countless clicks every day!

It's a simple plugin which overrides Beaver Builder's colour picker behaviour, making your colour presets visible first by default instead of just the picker, streamlining your development workflow.

## Compatibility

- **Beaver Builder 2.9+**: Compatible with the new React-based color picker
- **Beaver Builder < 2.9**: Uses the classic color picker override

The plugin automatically detects which version of Beaver Builder you're using and applies the appropriate method.

---

## Installation from GitHub
When installing this plugin from GitHub:
1. Go to the [Releases](https://github.com/weavedigitalstudio/bb-color-presets-first/releases) page
2. Download the latest release ZIP file
3. Extract the ZIP file on your computer
4. Rename the extracted folder to remove the version number  
   (e.g., from `bb-color-presets-first-1.1.0` to `bb-color-presets-first`)
5. Create a new ZIP file from the renamed folder
6. In your WordPress admin panel, go to Plugins → Add New → Upload Plugin
7. Upload your new ZIP file and activate the plugin

**Note**: The folder renaming step is necessary for WordPress to properly handle plugin updates and functionality.

---

## How It Works

### Beaver Builder 2.9+
With the new React-based color picker, the plugin automatically clicks the presets tab whenever a color picker dialog opens.

### Beaver Builder < 2.9
When you click any colour picker in Beaver Builder, the plugin automatically shows the presets panel instead of requiring you to click the show presets button first.

### Color Restriction
The plugin also hides UI elements that allow adding custom colors. This helps maintain brand consistency by requiring users to use only the predefined color palettes from your Global Colors.

There are no settings. It just works.

### Allowing Custom Colors
If you want to allow users to add custom colors, you can either:
1. Comment out the `bb_color_presets_restrict_color_palette` function in the plugin file
2. Remove the action hooks at the bottom of that function

---

## Requirements
- WordPress
- Beaver Builder (any version)

## Change Log

### 1.1.2
- Fixed incorrect url. If you had v1.1.1 you'll need to re-install for the auto updates to work. 

### 1.1.1
- Improved BB 2.9+ color picker integration with inline script
- Removed unused JS files
- Optimized code structure

### 1.1.0
- Added compatibility with Beaver Builder 2.9+ and its React-based color picker
- Improved version detection to load the appropriate script
- Updated documentation

### 1.0.1
- Initial release

## Support

Found a bug? [Open an issue](https://github.com/weavedigital/bb-color-presets-first/issues)
