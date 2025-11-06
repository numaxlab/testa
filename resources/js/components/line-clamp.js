export default () => ({
    showMore: false,

    init () {
        this.$nextTick(() => {
            if (this.$refs.description.scrollHeight === this.$refs.description.clientHeight) {
                this.showMore = true;
            }
        });
    },
});
