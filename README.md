# social_pages (Drupal 8/9 module)

#Add links to popular social media accounts for drupal user and profile entities.

This module provides a field type wiget and formatter that can be attached to entities (usually user profiles) for users to add links to their social media accounts.

I find the UI on this much easier for users compared to other social media modules in contrib such as https://www.drupal.org/project/social_link_field

# Supported social networks
Supports the following networks which are commonly used on the sites I maintain.
- Facebook
- Twitter
- Instagram
- Pinterest
- LinkedIn
- 500px
- Vimeo
- Youtube
- Flickr
- Snapchat

Supports themeing the links with FontAwesome icons but that needs to be included in your theme manually. This module does not add the FontAwesome library.
You can still use the other formatters without FA.

It's a little rough around the edges but it works well for my use cases.

# Usage
- Install and enable it like any other module
- Add a "social_pages" field to your entity
- Choose the enabled networks you want the user to be able to edit in the form settings
- Choose the enabled networks you want to show up on the front end in the fields display formatter
