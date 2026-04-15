# WordPress Developer Technical Trial

## Objective

Update the RSS feeds for a sports publisher website to comply with Yahoo Sports syndication requirements, while maintaining existing MSN syndication compliance.

## Parameters

- The maximum time limit for this task is 4 hours.
- The site runs WordPress and currently publish a standard RSS feed as well as a custom alternate-headline RSS feed (accessible via feed URLs ending in `/alt`).
- All changes must be applied to both the standard and alternate RSS feeds.
- Changes must not break existing MSN syndication compliance.
- Do not modify post content or the post editing interface.

## Background

The client has a sports publisher website that is being onboarded as Yahoo Sports syndication partners. It also syndicates content to MSN, which has its own RSS formatting requirements already in place.

The current RSS feeds include a custom alternate-headline feature. This allows editors to enter an alternative headline on the post edit screen. A separate RSS feed displays this alternate headline instead of the standard post title. These alternate feed URLs end in `/alt`.

Yahoo has provided initial feedback identifying three issues that must be resolved:

- Images in the feed use `srcset` attributes, which Yahoo does not support. Images should be delivered at the best available resolution using standard `img src` tags, wrapped in `figure` tags. Each image should include a `figcaption` tag for both the image caption and image credit. Currently, no captions or credits are present in the feed.
- The `description` tag contains HTML and images. Yahoo requires the `description` tag to contain only a plain-text summary of the article. All images must be moved to the `content:encoded` tag only.
- Comments are included in the feed and are not supported by Yahoo.

## Deliverables

- Updated the RSS feed that passes Yahoo's RSS validation requirements.
- Confirmation that existing MSN syndication compliance is unaffected.
- Project documentation:
  - A link to your fork of the repository.
  - Time spent completing the project.
  - Summary of the technical approach, including how feed output was modified (e.g., theme functions, plugin configuration, custom feed templates).
  - Explanation of how MSN compliance was preserved alongside the Yahoo changes.
  - Any edge cases or tradeoffs identified.

## Process

1. Fork this repository.
2. Make all changes in your fork.
3. Upload your changes to the server via SFTP
4. Review the current feed output for both the standard and `/alt` feeds before making any changes.
5. Implement Yahoo-compliant feed formatting across both feed types.
6. Verify MSN feed requirements remain intact after changes.
7. Submit a link to your fork when complete.

## Extra Credit

- If time permits, document any additional RSS feed issues observed that were not part of the original Yahoo feedback.
- Suggest a testing or monitoring approach to catch feed regressions in the future.

## Notes

- WordPress dashboard and SFTP credentials will be provided in a separate email.