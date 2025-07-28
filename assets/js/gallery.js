$(document).ready(function () {
  const $grid = $(".gallery-grid");

  $.getJSON("/api/gallery.php?method=first-images", function (images) {
    if (Array.isArray(images) && images.length > 0) {
      images.forEach(function (src, index) {
        // Extract the folder name as work ID (assumes last 3 segments are /gallery/{id}/{filename})
        const parts = src.split("/");
        const workId = parts[parts.length - 2];

        const $item = $(`
          <div class="gallery-item">
            <a href="work.html?workId=${workId}">
              <img src="${src}" alt="Gallery ${workId}" />
            </a>
          </div>
        `);

        $grid.append($item);
      });
    } else {
      $grid.append(`<p>No images found.</p>`);
    }
  }).fail(function () {
    $grid.append(`<p>Error loading gallery.</p>`);
  });
});
