/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React from 'react';
import Translate from 'react-translate-component';
import DismissableAlertBox from '../../app/markup/DismissableAlertBox';

/**
 * Infobox which renders the information alert box in the create account page.
 *
 * @returns {React.Element} The markup.
 */
const InfoBox = () => {
  return (
    <DismissableAlertBox bsStyle="info">
      <Translate content="pages.portal.create_account.info_box" />
    </DismissableAlertBox>
  );
};

export default InfoBox;
