self.addEventListener('sync', function(event) {
  if (event.tag == 'saveTransaction') {
    event.waitUntil(saveTransaction());
  }
});
