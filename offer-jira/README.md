# Offer integration Jira board

This folder contains copy-paste ready Jira import artifacts for the Offer Detail implementation.

## Files

- ofr-backlog-jira.csv: CSV import file for Jira Cloud/Server.
- ofr-ticket-details.md: Human-readable ticket details with acceptance criteria.

## Jira import

1. Go to Jira Settings > System > External System Import > CSV.
2. Select ofr-backlog-jira.csv.
3. Map fields:
   - Summary -> Summary
   - Issue Type -> Issue Type
   - Description -> Description
   - Priority -> Priority
   - Labels -> Labels
   - Epic Name -> Epic Name (for epic rows)
   - Epic Link -> Epic Link (for story rows)
4. Import Epics first if your Jira setup requires two-pass import.
