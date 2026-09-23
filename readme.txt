=== TopDown Slider ===
Contributors: ozanocak
Tags: slider, fullscreen, landing page, react, scroll
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create full-screen, scroll-driven landing pages with a modern React admin.

== Description ==

TopDown Slider lets you build full-screen vertical slide experiences — the kind of scroll-snap landing page you see on SpaceX, Apple, and modern marketing sites.

**Features**

* Full-screen vertical slides with scroll-snap navigation
* Per-slide image, title, description, and optional CTA button
* Per-slide text and image animations (fade, slide, zoom, pan)
* Per-slide text positioning (top-left, top-right, center, bottom-left, bottom-right)
* Desktop top nav, tablet/mobile slide-in panel
* Multiple sliders — create and embed as many as you need
* Shortcode: `[topdown_slider id="123"]`
* REST API backed, React admin panel
* Accessibility: respects `prefers-reduced-motion`

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/topdown-slider/`, or install through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Go to **TopDown Sliders → Add New Slider**.
4. Add slides, save.
5. Copy the shortcode shown in the editor header.
6. Paste the shortcode into any page or post.

== Frequently Asked Questions ==

= How do I create a slider? =

Go to **TopDown Sliders** in the admin sidebar and click **Add New Slider**.

= How do I embed the slider? =

Copy the shortcode from the editor header — it looks like `[topdown_slider id="5"]` — and paste it into any page or post.

= Can I have more than one slider? =

Yes. Create as many sliders as you want, each with its own shortcode.

== Screenshots ==

1. The slider list page
2. The slide editor with all options
3. A full-screen slide on the frontend
4. The mobile slide-in navigation panel

== Changelog ==

= 0.1.0 =
* Initial release.