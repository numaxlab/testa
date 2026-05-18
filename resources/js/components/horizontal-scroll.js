export default () => ({
    scrollInterval: null,

    startScroll(direction) {
        this.stopScroll();
        this.scrollInterval = setInterval(() => {
            this.$refs.scrollContainer.scrollBy({
                left: direction,
                behavior: 'smooth',
            });
        }, 100);
    },

    stopScroll() {
        if (this.scrollInterval) {
            clearInterval(this.scrollInterval);
            this.scrollInterval = null;
        }
    },
});
