import {existsSync} from "node:fs";
import {cp, rm} from "node:fs/promises";
import {env} from "node:process";
import {deleteAsync} from "del";
import {execa} from "execa";
import gulp from "gulp";
import replace from "gulp-replace";
import composer from "./composer.json" assert {type: "json"};

/** Deletes all generated files. */
export function clean() {
	return deleteAsync("var/**/*");
}

/** Builds the documentation. */
export async function doc() {
	await rm("docs", {force: true, recursive: true});
	await exec("phpdoc", ["--config=etc/phpdoc.xml"]);
	return cp("www/favicon.ico", "docs/images/favicon.ico");
}

/** Installs the project dependencies. */
export async function install() {
	await exec("composer", [existsSync("composer.lock") ? "install" : "update"]);
	return exec("npm", [existsSync("package-lock.json") ? "install" : "update"]);
}

/** Performs the static analysis of source code. */
export async function lint() {
	await exec("vendor/bin/phpstan", ["analyse", "--configuration=etc/phpstan.neon"]);
	return exec("tsc", ["--project", "jsconfig.json"]);
}

/** Publishes the package. */
export async function publish() {
	for (const command of [["tag"], ["push", "origin"]]) await exec("git", [...command, `v${composer.version}`]);
}

/** Runs the test suite. */
export function test() {
	env.XDEBUG_MODE = "coverage";
	return exec("vendor/bin/phpunit", ["--configuration=etc/phpunit.xml"]);
}

/** Updates the version number in the sources. */
export function version() {
	return gulp.src(["package.json", "etc/phpdoc.xml"], {base: "."})
		.pipe(replace(/"version": "\d+(\.\d+){2}"/, `"version": "${composer.version}"`))
		.pipe(replace(/version number="\d+(\.\d+){2}"/, `version number="${composer.version}"`))
		.pipe(gulp.dest("."));
}

/** Runs the default task. */
export default gulp.series(
	clean,
	version
);

/**
 * Runs the specified command.
 * @param {string} command The command to run.
 * @param {string[]} [args] The command arguments.
 * @param {import("execa").Options} [options] The child process options.
 * @returns {import("execa").ExecaChildProcess} Resolves when the command is finally terminated.
 */
function exec(command, args = [], options = {}) {
	return execa(command, args, {preferLocal: true, stdio: "inherit", ...options});
}
