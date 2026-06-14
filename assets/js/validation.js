function initPhotoPreview(inputElementId, previewContainerId) {
  var input = document.getElementById(inputElementId);
  var container = document.getElementById(previewContainerId);
  if (!input || !container) return;

  input.addEventListener('change', function() {
    container.innerHTML = '';
    var files = input.files;

    if (files.length < 1 || files.length > 6) {
      var errorDiv = document.createElement('div');
      errorDiv.className = 'text-red-500 text-sm p-3 bg-red-50 rounded-xl';
      errorDiv.textContent = 'يجب اختيار من 1 إلى 6 صور';
      container.appendChild(errorDiv);
      input.value = '';
      return;
    }

    var allowed = ['jpg', 'jpeg', 'png', 'webp'];
    var maxSize = 5242880;

    for (var i = 0; i < files.length; i++) {
      var ext = files[i].name.split('.').pop().toLowerCase();
      if (allowed.indexOf(ext) === -1) {
        var errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm p-3 bg-red-50 rounded-xl';
        errorDiv.textContent = 'الصيغة غير مدعومة. الصيغ المسموحة: JPG, PNG, WEBP';
        container.appendChild(errorDiv);
        input.value = '';
        return;
      }
      if (files[i].size > maxSize) {
        var errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm p-3 bg-red-50 rounded-xl';
        errorDiv.textContent = 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت';
        container.appendChild(errorDiv);
        input.value = '';
        return;
      }
    }

    var grid = document.createElement('div');
    grid.className = 'grid grid-cols-3 gap-2 mt-4';

    for (var i = 0; i < files.length; i++) {
      (function(file) {
        var reader = new FileReader();
        reader.onload = function(e) {
          var img = document.createElement('img');
          img.src = e.target.result;
          img.width = 100;
          img.height = 100;
          img.className = 'object-cover rounded-lg border border-gray-200';
          grid.appendChild(img);
        };
        reader.readAsDataURL(file);
      })(files[i]);
    }

    container.appendChild(grid);
  });
}

function validatePhotos(inputId) {
  var input = document.getElementById(inputId);
  if (!input) return { valid: false, error: 'حقل الصور غير موجود' };

  var files = input.files;

  if (files.length < 1 || files.length > 6) {
    return { valid: false, error: 'يجب اختيار من 1 إلى 6 صور' };
  }

  var allowed = ['jpg', 'jpeg', 'png', 'webp'];
  var maxSize = 5242880;

  for (var i = 0; i < files.length; i++) {
    var ext = files[i].name.split('.').pop().toLowerCase();
    if (allowed.indexOf(ext) === -1) {
      return { valid: false, error: 'الصيغة غير مدعومة. الصيغ المسموحة: JPG, PNG, WEBP' };
    }
    if (files[i].size > maxSize) {
      return { valid: false, error: 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت' };
    }
  }

  return { valid: true, error: '' };
}

document.addEventListener('DOMContentLoaded', function() {
  if (document.getElementById('photos')) {
    initPhotoPreview('photos', 'photoPreview');
  }
});
