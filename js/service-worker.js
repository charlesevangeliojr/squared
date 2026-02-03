// service-worker.js
self.addEventListener("install", () => {
    console.log("Service Worker installed.");
  });
  
  self.addEventListener("fetch", (event) => {
    // Just pass through requests for now
  });
  