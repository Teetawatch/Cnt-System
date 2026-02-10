---
description: Process for answering questions using ONLY the antigravity-awesome-skills/skills folder
---

1.  **Search Skills**: specific search method must be used to find relevant information within `antigravity-awesome-skills/skills`.
    -   Use `find_by_name` or `grep_search` to locate relevant skill files based on keywords in the user's request.
    -   Do NOT rely on general knowledge.

2.  **Verify Information**: Open the found skill files using `view_file` to read their contents.

3.  **Formulate Response**:
    -   If relevant information is found: Construct the answer using **only** the information present in the skill files.
    -   If **no** relevant information is found or it is insufficient: You MUST reply with the exact phrase: "ไม่พบข้อมูลในโฟลเดอร์ทักษะ".
    -   Do not use outside knowledge, assumptions, or hallucinations.

4.  **Citation**: (Optional but recommended) Mention which skill file the information came from.
