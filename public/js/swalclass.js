const enTypeIcon = ["success", "error", "warning", "info", "question", "none"];
class SwalCustomAlert {
    static SwalFireWithoutQuit(title, message, en) {
      if (enTypeIcon.includes(en)) {
        if (en == "none") {
          Swal.fire({
            title: title,
            html: message,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            hideOnOverlayClick: false,
            hideOnContentClick: false,
          });
        } else {
          Swal.fire({
            icon: en,
            title: title,
            html: message,
            showConfirmButton: false,
            allowOutsideClick: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            hideOnOverlayClick: false,
            hideOnContentClick: false,
          });
        }
      } else {
        Swal.fire({
          icon: "error",
          title: title,
          text: message,
          showConfirmButton: false,
          allowOutsideClick: false,
          allowOutsideClick: false,
          allowEscapeKey: false,
          hideOnOverlayClick: false,
          hideOnContentClick: false,
        });
      }
    }
    static SwalInfo(title, message, en) {
      if (enTypeIcon.includes(en)) {
        if (en == "none") {
          Swal.fire({
            title: title,
            html: message,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            hideOnOverlayClick: false,
            hideOnContentClick: false,
            confirmButtonText: "OK",
          });
        } else {
          Swal.fire({
            icon: en,
            title: title,
            html: message,
            showConfirmButton: true,
            allowOutsideClick: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            hideOnOverlayClick: false,
            hideOnContentClick: false,
            confirmButtonText: "OK",
          });
        }
      } else {
        Swal.fire({
          icon: "error",
          title: title,
          html: message,
          showConfirmButton: true,
          allowOutsideClick: false,
          allowOutsideClick: false,
          allowEscapeKey: false,
          hideOnOverlayClick: false,
          hideOnContentClick: false,
          confirmButtonText: "OK",
        });
      }
    }
  }