export const slugify = (text) => {
  return text
    .toLowerCase()
    .trim()
    .replace(/[^\w\s]/g, "") // hapus karakter selain huruf, angka, underscore, spasi
    .replace(/\s+/g, "_"); // ganti spasi dengan underscore
};
