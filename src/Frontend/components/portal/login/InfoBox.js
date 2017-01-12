/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React     from 'react';
import Translate from 'react-translate-component';

/**
 * InfoBox that will be rendered on the left to the login box.
 *
 * @returns {React.Element} The markup for the infobox.
 */
export default () => {
  return (
    <div>
      <div className="info-div-text">
        <Translate content="pages.portal.login.info_text" />
      </div>
    </div>
  );
};
