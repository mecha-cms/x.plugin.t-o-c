Table of Contents Extension for [Mecha](https://github.com/mecha-cms/mecha)
===========================================================================

![Table of Contents](https://user-images.githubusercontent.com/1669261/120205872-aa970f00-c254-11eb-8de7-801ca23c6e08.png)

---

Release Notes
-------------

### 2.5.2

 - [@mecha-cms/mecha#96](https://github.com/mecha-cms/mecha/issues/96)

### 2.5.1

 - Make block example becomes copy-paste friendly.
 - Separate functions by its duty: `_\lot\x\t_o_c` as the main hook function, `_\lot\x\t_o_c\content` as the generic table of contents generator, `_\lot\x\t_o_c\block` as table of contents generator that depends on _Block_ extension.

### 2.5.0

 - Removed `toc` property as a way to enable or disable table of contents feature on specific page. Store your custom table of contents state in the `state` property from now on.
 - You can now toggle the table of contents’ list visibility by clicking on the table of contents title.