(function () {
  document.addEventListener("DOMContentLoaded", function () {
    // Get all anchor tags in the document
    const links = document.querySelectorAll("a");

    // Iterate through each link
    links.forEach((link) => {
      const href = link.getAttribute("href");
      const isExternal = href && link.hostname !== window.location.hostname;
      const isPdf = href && href.endsWith(".pdf");

      // Check if the link is external or a PDF
      if (isExternal || isPdf) {
        // Set target to '_blank' to open in a new tab
        link.target = "_blank";

        // Optional: Add rel="noopener noreferrer" for security reasons
        link.rel = "noopener noreferrer";
      }
    });
  });
})();
