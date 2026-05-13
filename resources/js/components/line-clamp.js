export default () => ({
    expanded: false,
    clamped: false,

    init () {
        this.$nextTick(() => {
            this.clamped = this.$refs.description.scrollHeight > this.$refs.description.clientHeight;
        });
    },
});
