function initPageFragment(fragUrl) {
  switch(fragUrl) {
    case "page-fragments/voting.php": 
      initVoting();
      break;
    default:
      showContent();
      break;
  }
}
