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

import { shallow } from 'enzyme';
import React from 'react';
import InversedGrid from '../../../../components/app/markup/InversedGrid';
import { expect } from 'chai';

describe('InversedGrid', () => {
  it('renders the two content items into the inverse-container structure', () => {
    const markup = shallow((
      <InversedGrid>
        <div>First</div>
        <div>Second</div>
      </InversedGrid>
    ));

    expect(markup.find('Row > [className="grid-item-1"]').contains(<div>First</div>)).to.equal(true);
    expect(markup.find('Row > [className="grid-item-2"]').contains(<div>Second</div>)).to.equal(true);

    expect(markup.find('Row').hasClass('inversed-column-container')).to.equal(true);
  });

  it('fails with invalid amount of sub-containers', () => {
    expect(() => {
      shallow((
        <InversedGrid>
          <div>First</div>
        </InversedGrid>
      ))
    }).to.throw('This element requires exactly 2 children!');
  });
});
