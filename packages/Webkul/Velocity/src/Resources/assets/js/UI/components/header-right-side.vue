<template>
    <ul type="none" class="no-margin hello">
        <li v-for="(content, index) in headerContent" :key="index" class='hello'>
            <div
                :class="`sub-categories sub-category cursor-default`"
            > 
                <nav
                    class="sidebar"
                    id="sidebar-level"
                >
                    <ul type="none">
                        <li>
                            <a
                                :id="`sidebar-level-link-2-`"
                                :class="`category sub-category unset`"
                                :href="`${$root.baseUrl}/${content['child_link']}`"
                            >
                                <div
                                    class="category-icon"
                                >
                                </div>
                                <span class="category-title" v-text="content.child"></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <a
                v-text="content.title"
                :href="`${$root.baseUrl}/${content['page_link']}`"
                v-if="
                    content['content_type'] == 'link' ||
                        content['content_type'] == 'category'
                "
                :target="content['link_target'] ? '_blank' : '_self'"
            >
            </a>
        </li>
    </ul>
</template>

<script>
export default {
    props: ['headerContent']
};
</script>
