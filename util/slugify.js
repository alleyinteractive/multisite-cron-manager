/**
 * Given a string, slugifies it by transforming it to lowercase,
 * dropping non-alphanumeric characters, and replacing spaces with dashes.
 * @param {string} text - The text to slugify.
 * @returns {string} - The slugified text.
 */
export default function slugify(text) {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s/g, '-');
}
