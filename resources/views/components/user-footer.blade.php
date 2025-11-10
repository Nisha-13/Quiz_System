<footer class="bg-teal-600 text-white py-6 mt-24 text-center">
    <div class="max-w-7xl mx-auto px-6 space-y-3">
        <p class="text-sm">&copy; 2025 Quiz System. All Rights Reserved.</p>

        <div class="flex justify-center space-x-4 text-lg">
            <a href="#" class="hover:text-yellow-200"><i class="bi bi-facebook"></i></a>
            <a href="#" class="hover:text-yellow-200"><i class="bi bi-twitter"></i></a>
            <a href="#" class="hover:text-yellow-200"><i class="bi bi-instagram"></i></a>
            <a href="#" class="hover:text-yellow-200"><i class="bi bi-linkedin"></i></a>
        </div>
    </div>
</footer>


<!-- Optional: Toggle Menu Script -->
<script>
    const toggleBtn = document.getElementById("menu-toggle");
    const mobileMenu = document.getElementById("mobile-menu");

    toggleBtn.addEventListener("click", () => {
        if (mobileMenu.style.maxHeight) {
            mobileMenu.style.maxHeight = null;
        } else {
            mobileMenu.style.maxHeight = mobileMenu.scrollHeight + "px";
        }
    });
</script>