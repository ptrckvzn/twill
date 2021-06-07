import { mapGetters, mapState } from 'vuex'
import { BLOCKS } from '@/store/mutations'

export default {
  props: {
    section: {
      type: String,
      required: true
    }
  },
  computed: {
    availableBlocks () {
      return this.getAvailableBlocks(this.section)
    },
    savedBlocks () {
      return this.blocks(this.section)
    },
    hasBlockActive () {
      return Object.keys(this.activeBlock).length > 0
    },
    ...mapState({
      activeBlock: state => state.blocks.active
    }),
    ...mapGetters({
      getAvailableBlocks: 'availableBlocks',
      blocks: 'blocks',
      sections: 'sections'
    })
  },
  methods: {
    reorderBlocks (value) {
      this.$store.commit(BLOCKS.REORDER_BLOCKS, {
        section: this.section,
        value: value
      })
    },
    moveBlock ({ oldIndex, newIndex }) {
      this.$store.commit(BLOCKS.MOVE_BLOCK, {
        section: this.section,
        oldIndex,
        newIndex
      })
    }
  },
  render () {
    return this.$scopedSlots.default({
      availableBlocks: this.availableBlocks,
      savedBlocks: this.savedBlocks,
      reorderBlocks: this.reorderBlocks,
      moveBlock: this.moveBlock,
      sections: this.sections,
      hasBlockActive: this.hasBlockActive,
      activeBlock: this.activeBlock
    })
  }
}
