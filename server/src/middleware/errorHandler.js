function errorHandler(err, req, res, next) {
  console.error(err);
  if (err.name === "ZodError") {
    return res.status(400).json({ message: "Validation failed", errors: err.errors });
  }
  if (err.code === "P2002") {
    return res.status(409).json({ message: `Duplicate value for field: ${err.meta?.target}` });
  }
  if (err.code === "P2025") {
    return res.status(404).json({ message: "Record not found" });
  }
  res.status(err.status || 500).json({ message: err.message || "Internal server error" });
}

module.exports = errorHandler;
