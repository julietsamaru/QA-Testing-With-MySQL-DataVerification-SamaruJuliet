# QA Testing With MySQL Data Verification

## Repository Overview

This educational quality assurance toolkit tests **Kara David's Wish Note**, a single-file PHP application backed by MariaDB. It demonstrates why a successful interface message must be checked against the data actually stored in a database.

The application intentionally omits `Location` from its insertion query. Preserve this flaw during the exercise so that backend verification can reveal it. Planned tests use fictional records. The supplied exploratory screenshots also contain a user-entered name; the assignment-defined table name does not make allegations about that person or any real person.

## Repository Contents

- **Part A — `readme.md`:** Repository overview and formatted test case documentation.
- **Part B — `webapp-generation-prompt.txt`:** Prompt used to generate the application.
- **Part C — `test-webapp.php`:** PHP application with embedded HTML, CSS, and vanilla JavaScript.

Supplemental files: `TestCase.txt` contains the execution worksheet; `setup.sql` creates the local database and a fictional baseline record; `evidence/` contains the supplied modal and database screenshots. The assignment's required filename reference is **textcase.txt**. The actual supplied worksheet is `TestCase.txt`; filename case matters on Ubuntu.

## Test Case Documentation

| Property | Value |
| --- | --- |
| Test Case ID | TC-DataEntry-001 |
| Title | Data Entry - Valid |
| Description | Functional testing of the Data Entry page with backend validation using MySQL queries. |
| Preconditions | App installed locally; MariaDB running; database pre-populated for comparison; latest available Chrome/Firefox browser; JavaScript enabled. |
| Observed test data | Five submitted records (ids 2–6) plus one setup baseline. Actual records differ from the originally suggested examples. |
| Execution status | Failed: all five submitted Location values differ from the stored NULL values. Ids 2–3 are supported by form/modal screenshots; entered locations for ids 4–6 are tester-reported. Cancellation passed in the separate rerun: count remained 6 and form values were retained. |
| Observed environment | Phone browser at localhost and MariaDB terminal; software versions not supplied. |
| Evidence compilation date | 12 October 2026; execution date and tester not supplied. |

### Execution Table

The source worksheet's blanks initially used **Pending Execution**. This version records screenshot evidence and tester-reported values, distinguishing them from unverified observations. The database screenshot establishes five submitted records plus one setup baseline. The tester supplied the remaining entered locations. Earlier counts of 2 had uncertain timing; a subsequent cancellation rerun using cancel / check / Test mayor / silay supplies the closed-modal form and equal before/after counts of 6.

| Step | Action | Expected Result | Actual Result |
| --- | --- | --- | --- |
| a | Verify label order and writable text boxes. | Labels appear in order: "Target's First Name", "Target's Last Name", "Target's Government Position", "Target's Location"; all text boxes are writable. | Observed: the populated form shows all four labels in the correct order and values in all four inputs. |
| b | Verify the "Send Wish Note" button. | The button changes color on hover and is not disabled. | Button is displayed and a modal opening is evidenced. Hover remains unverified; on a touch-only device it is not applicable. |
| c | Enter valid test data into the respective fields. | The fields accept valid test data. | Five submitted records are stored as ids 2–6. Stored names and positions fit their limits. Locations are evidenced for ids 2–3 and tester-reported as silay / bcd / ambot for ids 4–6, also within their limit. |
| d | Click "Send Wish Note" to display the modal. | A centered modal opens with the heading "Verify Target Data". | Observed: the screenshot shows the centered modal and correct heading. |
| e | Compare all modal values with the test data. | All four values match the entered record, including Location. | **Passed for the supplied second preview:** form and modal both contain test / Test / Test / Test, including matching Location. |
| f | Click "Cancel". | Modal closes; inputs remain; no row is inserted. | **Passed for the separate cancellation rerun:** modal is closed; cancel / check / Test mayor / silay remains in the form; before/after counts both equal 6. |
| g | Click "Send Wish Note" again, then "Confirm & Send". | Confirmation submits the POST request and creates exactly one new row. | Latest query shows five submitted records, ids 2–6, in addition to baseline id 1. Confirmation sequence and count change for each individual submission were not independently captured. |
| h | Verify the successful entry message on the Data Entry page. | "Success! The wish note has been sent." appears. | **Passed for message display:** the form screenshot and dedicated success-message screenshot show the exact success message. Attribution to a particular submission is not established. |
| i | Query the database and compare all saved fields with the submitted data. | All four saved values match the submitted record. | **Failed:** all five submitted Locations are NULL instead of silay / Test / silay / bcd / ambot for ids 2–6 respectively. Last three expected values are tester-reported. Latest result: 6 rows total. |

On a touch-only phone, record hover behavior as not applicable and check it separately on a device with a pointer. Repeat c–i for each record.

### Test Data

These are the actual submitted records transcribed from the latest screenshot. The expected entered locations come from earlier form/modal evidence where available; they are not inferred from the missing database values.

| Database id | First Name | Last Name | Government Position | Entered / previewed Location | Stored Location |
| --- | --- | --- | --- | --- | --- |
| 2 | Cyrus Bryen | lim | single | silay | NULL |
| 3 | test | Test | Test | Test | NULL |
| 4 | Alex | t1 | Test Mayor | silay (tester-reported) | NULL |
| 5 | Hello | Hii | Ambot | bcd (tester-reported) | NULL |
| 6 | Cassie | Wala | Secretary | ambot (tester-reported) | NULL |

The tester clarified the exact remaining values: Alex / t1 used `silay`, Hello / Hii used `bcd`, and Cassie / Wala used `ambot`. Preserve these literal strings; `bcd` has not been expanded to `bacolod`.

The government-position strings `single`, `Test`, and `Ambot` are accepted as nonempty text by the application; they do not represent the suggested fictional position names. This does not change the observed Location mismatch.

### Originally Suggested Examples

These examples were provided before execution. The observed records above are the actual test data; do not claim these suggestions were submitted.

| ID | First Name | Last Name | Government Position | Location |
| --- | --- | --- | --- | --- |
| QA001 | Alex | TestOne | Test Mayor | Test City Alpha |
| QA002 | Bea | TestTwo | Test Councilor | Test City Beta |
| QA003 | Casey | TestThree | Test Governor | Test Province Gamma |
| QA004 | Dani | TestFour | Test Secretary | Test District Delta |
| QA005 | Eli | TestFive | Test Treasurer | Test Town Epsilon |

### Completed Expected Behavior

For d–h, expect a modal headed **Verify Target Data**, an exact preview of all four values, cancellation without saving or clearing the input, confirmation that submits exactly one row, and the message **Success! The wish note has been sent.** These are expectations, not recorded outcomes.

For i, all four stored fields should match the submitted record. A missing `Location` is a failed result even if the other fields and success message are correct.

## Local Installation in Termux / Ubuntu

Run this exercise locally. Passwordless root access is included to match the assignment and is unsuitable for a public deployment.

1. Open Termux and enter your already installed Ubuntu environment:

   ```sh
   proot-distro login ubuntu
   ```

2. Install the local web and database packages:

   ```sh
   apt update
   apt install apache2 mariadb-server mariadb-client php libapache2-mod-php php-mysql
   mysqld_safe &
   ```

   Press Enter after the background command. If your package supplies `mariadbd-safe` instead, use that executable. These are setup instructions; this toolkit has not been tested in your phone's proot environment.

3. Log in with `mysql -u root` (or `mariadb -u root`) and configure the assignment's local MariaDB account:

   ```sql
   ALTER USER 'root'@'localhost' IDENTIFIED VIA mysql_native_password USING PASSWORD('');
   exit;
   ```

   This is MariaDB authentication syntax. See the official [ALTER USER documentation](https://mariadb.com/docs/server/reference/sql-statements/account-management-sql-statements/alter-user).

4. Download this repository's files to a directory accessible inside Ubuntu, change to that directory, and initialize the schema:

   ```sh
   mysql -u root < setup.sql
   cp test-webapp.php /var/www/html/test-webapp.php
   service apache2 restart
   ```

   The supplemental schema inserts one baseline record only when the table is empty. It does not replace an existing table: use `SHOW COLUMNS FROM main_corrupt_politicians;` to inspect an existing installation for compatible fields and defaults.

5. Keep Termux running and open **http://localhost/test-webapp.php** in your phone's browser. If Apache uses a different port, include that port in the URL.

### Corrections to the Supplied Instructions

- Use `apt update`, rather than `apt get update`.
- The supplied `wget URL | bash` command does not explicitly send the downloaded script body to Bash. If your instructor requires that installer, download it to a named file, inspect it, and then run it with `bash filename`. Its contents could not be retrieved during preparation of this toolkit, so it was not executed.
- This repository provides a direct package/schema setup to make the supplied files usable without that installer.
- Opening `/test-webapp.php` selects this application explicitly; bare `localhost` may display Apache's default page.

## Database Verification

### 6i — Query

From Ubuntu, run `mysql -u root`, then execute:

```sql
USE birthday_wishnote;
SELECT * FROM main_corrupt_politicians;
```

Capture this output before entry, after cancellation, and after confirmation. For row counts:

```sql
SELECT COUNT(*) AS total_rows FROM main_corrupt_politicians;
```

### 7i — Expected Result

Cancellation leaves the baseline unchanged. Each confirmation creates one additional row containing all four entered values. Five completed submissions increase the row count by five from the recorded baseline.

### 8i — Actual Result

The supplied [database screenshot](evidence/database-results.png) shows the following results. This is a transcription of the wrapped phone-terminal output:

| id | FirstName | LastName | GovPosition | Location |
| --- | --- | --- | --- | --- |
| 1 | Baseline | QARecord | Test Position | Test Location |
| 2 | Cyrus Bryen | lim | single | NULL |

`SELECT * FROM main_corrupt_politicians;` returned **2 rows in set (0.007 sec)**. The count query returned **total_rows = 2**, with **1 row in set (0.002 sec)**.

The [modal screenshot](evidence/modal-preview.png) displays **Location: silay**, while database row id 2 stores **NULL**. The first three values match the preview. This confirms step i failed for the observed record.

The [latest database screenshot](evidence/database-five-submissions.png) provides the subsequent complete result:

| id | FirstName | LastName | GovPosition | Location |
| --- | --- | --- | --- | --- |
| 1 | Baseline | QARecord | Test Position | Test Location |
| 2 | Cyrus Bryen | lim | single | NULL |
| 3 | test | Test | Test | NULL |
| 4 | Alex | t1 | Test Mayor | NULL |
| 5 | Hello | Hii | Ambot | NULL |
| 6 | Cassie | Wala | Secretary | NULL |

`SELECT * FROM main_corrupt_politicians;` returned **6 rows in set (0.001 sec)**: one baseline row and five submitted records. Every submitted Location is NULL. Expected values for ids 2–3 are **silay** and **Test**, supported by preview screenshots. The tester reports **silay**, **bcd**, and **ambot** for ids 4–6 respectively. All five nonempty entered values differ from the stored NULL values; the evidence for the final three expected values is the tester's statement.

### Additional UI and Cancellation Evidence

The [dedicated success-message screenshot](evidence/success-message.png) clearly displays **Success! The wish note has been sent.**, confirming step h's message-display requirement. The [populated form and success screenshot](evidence/form-success.png) shows the same message, the four labels in the required order, and these values:

| Field | Form value | Modal value |
| --- | --- | --- |
| FirstName | test | test |
| LastName | Test | Test |
| GovPosition | Test | Test |
| Location | Test | Test |

The [second modal screenshot](evidence/modal-test-record.png) matches the populated form in all four fields. The success message is already visible behind this modal, so it cannot by itself establish a new successful confirmation of this second record.

The [cancellation count screenshot](evidence/cancellation-counts.png) shows `SELECT COUNT(*) AS total_rows FROM main_corrupt_politicians;` twice. Both return **2**, with query times **0.001 sec** and **0.003 sec**. These are earlier counts, before the later six-row output. The tester answered **No / unsure** when asked whether they were taken before/after Cancel. They establish no net count change between those checks, but do not verify cancellation. The later database screenshot establishes the test / Test / Test record as id 3, with Location NULL.

The tester subsequently followed a dedicated cancellation procedure: capture the count, enter a distinct record, open the modal, tap Cancel, check retained inputs, and capture the count again. The [rerun form screenshot](evidence/cancel-rerun-form.png) shows the modal closed and **cancel / check / Test mayor / silay** retained. The [rerun count screenshot](evidence/cancel-rerun-counts.png) shows **6 before and 6 after**, with query times **0.024 sec** and **0.001 sec**. This supports **Passed** for step f in the separate rerun; no net row-count change occurred. The existing success banner on the form is not evidence that this cancellation record was submitted.

## Intentional Defect

| Property | Finding |
| --- | --- |
| Defect ID | BUG-001 |
| Affected column | `main_corrupt_politicians.Location` |
| Source-level cause | The INSERT names only `FirstName`, `LastName`, and `GovPosition`; `bind_param` binds only those three values. |
| Observed effect | All five submitted Locations are NULL instead of the expected nonempty values listed above. |
| Existing-schema caveat | A different default may produce an empty/default value; a required column without a default can make insertion fail. |
| Evidence | Source inspection, form/modal and live database screenshots, plus tester-reported entered locations for ids 4–6. All five mismatches are documented; ids 2–3 have preview evidence. |
| Correction after testing | Include `Location`, a fourth placeholder, and a fourth bound string in the insertion. |

Prepared statements bind values to placeholders; see the official [PHP mysqli::prepare documentation](https://www.php.net/manual/en/mysqli.prepare.php).

## Validation and Submission

PHP 8.5.1 syntax validation passed. Preparation checks using a simulated database passed for GET requests, valid submissions, optional Location, invalid/array input rejection, and connection/preparation/execution errors. A simulated DOM check passed for the four-field preview, cancellation without submission, validation, and confirmation. These checks confirmed that the insertion binds three fields and omits Location; they did not connect to a live MariaDB server or operate a real phone browser.

The project can be syntax-checked with `php -l test-webapp.php`. These automated checks are separate from the manual case above; do not mark it Passed without browser and database evidence.

Upload `readme.md`, `webapp-generation-prompt.txt`, and `test-webapp.php` to the repository. Include `TestCase.txt` and `setup.sql` so the exercise is reproducible. If using GitHub's web uploader, extract the toolkit archive and upload the individual files.

Five submitted records, their Location mismatches, the success message, and a passed separate cancellation rerun are documented. Hover remains unverified on the phone, and screenshots do not establish every repeated UI action for every submitted record. The overall **Failed** result is supported by the persistence defect.
