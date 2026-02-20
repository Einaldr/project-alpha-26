The git flow inside this repository follows Github Flow with following branch names:

    hontai -> main/master, default, long-lived branch. Every commit to this branch should be a working state of app.

    kairo/* -> development, daily workspace for building, always a branch of stable hontai branch. Merge to hontai when feature is done and delete the branch.

    hayate/* -> a hotfix branch, a quick hotfix branch branched off hontai for swift bugfixing. Merged back into hontai.

Additionally, this repository uses Git Tags:

    kansei/* -> milestone, marks a commit of hontai branch with a specified 'release-ready' version tag like kansei/v1.0.0.

