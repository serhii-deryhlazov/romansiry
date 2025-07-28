$(document).ready(function () {
  const $grid = $("#work-grid");

  function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
  }

  const workId = getQueryParam("workId");
  if (!workId) {
    $(".work-container").html("<p>No work selected.</p>");
    return;
  }

  $.getJSON(`/api/gallery.php?method=images-by-id&workId=${workId}`)
    .done(function (response) {
      const images = response.images;
      const context = response.context;

      if (Array.isArray(images) && images.length > 0) {
        // Set main image and thumbnails
        images.forEach((src, index) => {
          if (index === 0) {
            $("#main-image").attr("src", src);
          }

          const $thumb = $(`<img src="${src}" alt="Thumbnail ${index}" />`);
          if (index === 0) $thumb.addClass("active");

          $thumb.on("click", function () {
            $("#main-image").attr("src", src);
            $(".thumbnail-row img").removeClass("active");
            $thumb.addClass("active");
          });

          $("#thumbnails").append($thumb);
        });

        // Set title and description
        if (context) {
          $(".work-title").text(context.title || "Untitled Work");
          $(".work-description").text(context.description || "");
        }
      } else {
        $(".work-container").html("<p>No images found for this work.</p>");
      }
    })
    .fail(function () {
      $(".work-container").html("<p>Error loading work images.</p>");
    });
});
