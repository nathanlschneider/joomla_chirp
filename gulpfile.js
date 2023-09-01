const gulp = require('gulp');
const zip = require('gulp-zip');
const fs = require('fs');

// Define source directories for zipping
const sourceDir1 = 'plg_system_chirp';
const sourceDir2 = 'plg_system_chirp';
const sourceDir3 = 'com_chirp';

// Define the destination directory for zipped packages
const packagesDir = 'packages';

// Define the destination directory for the final zipped package with XML
const buildDir = 'build';

// Task to zip the two source directories and move them to the packages directory
gulp.task('zipDirs', () => {
  return gulp.src(sourceDir1 + '/**/*', { base: '.' })
  .pipe(zip(`${sourceDir1}.zip`))
  .pipe(gulp.dest(packagesDir))
  .pipe(gulp.src(sourceDir2 + '/**/*', { base: '.' }))
  .pipe(zip(`${sourceDir2}.zip`))
  .pipe(gulp.dest(packagesDir))
  .pipe(gulp.src(sourceDir3 + '/**/*', { base: '.' }))
  .pipe(zip(`${sourceDir3}.zip`))
  .pipe(gulp.dest(packagesDir))
});


// Task to zip the final package with the directories and XML file
gulp.task('zipFinalPackage', () => {
  const buildNumber = getBuildNumber();
  incrementBuildNumber();

  return gulp.src([packagesDir + '/**/*', 'pkg_chirp.xml'], { base: '.' })
    .pipe(zip(`pkg_chirp_${buildNumber}.zip`))
    .pipe(gulp.dest(buildDir));
});

// Read the build number from buildNumber.txt
function getBuildNumber() {
  return parseInt(fs.readFileSync('buildNumber.txt', 'utf8'));
}

// Increment the build number and write it back to buildNumber.txt
function incrementBuildNumber() {
  const currentBuildNumber = getBuildNumber();
  const newBuildNumber = currentBuildNumber + 1;
  fs.writeFileSync('buildNumber.txt', newBuildNumber.toString(), 'utf8');
}

// Define the default task to run all the tasks in sequence
gulp.task('default', gulp.series('zipDirs', 'zipFinalPackage'));
