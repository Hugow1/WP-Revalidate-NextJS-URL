# Revalidate NextJS URL

This plugin is used to revalidate the NextJS URL when saving a post or page. It is used in conjunction with the NextJS revalidate api.

This plugin is intended for the use with headless WordPress and NextJS. It assumes you have setup on demand revalidation in NextJS.

This can be done by adding the following file to your NextJS project:

```js
// pages/api/revalidate.js
export default async function handler(req, res) {
    const { path, token } = req.query;

    if (token !== process.env.SECRET_TOKEN) {
        return res.status(401).json({ message: "Invalid token" });
    } else if (path.length === 0) {
        return res.status(401).json({ message: "Path is required" });
    }

    try {
        await res.revalidate(path);
    } catch (err) {
        return res.status(500).send("Error revalidating page");
    }

    return res.status(200).json({
        revalidated: true,
        message: `Path ${path} revalidated successfully`,
    });
}
```
Make sure you set a `SECRET_TOKEN` in your environment variables.

## Installation
Download the plugin as a zip file and upload it to your WordPress site. Activate the plugin.
This will add a new settings page to your WordPress site. You will need to add a secret token to the settings page. This token will be used to authenticate the revalidation request. Also, you will need to add the URL to your NextJS project like this `https://example.com/api/revalidate/` or `http://localhost:3000/api/revalidate/`

Example:
<img width="597" alt="Screenshot 2022-11-29 at 14 50 24" src="https://user-images.githubusercontent.com/29499564/204546637-370063b9-6392-4a91-8b1a-c03e3d89263e.png">


## Usage
Once the plugin is installed and activated with the correct setting, this plugin will automatically revalidate the NextJS URL when a post or page is saved.
